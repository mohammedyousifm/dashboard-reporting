<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['budget', 'company', 'project', 'task']);

        if ($request->company_id) $query->where('company_id', $request->company_id);
        if ($request->budget_id)  $query->where('budget_id', $request->budget_id);
        if ($request->project_id) $query->where('project_id', $request->project_id);
        if ($request->status)     $query->where('status', $request->status);
        if ($request->from)       $query->where('expense_date', '>=', $request->from);
        if ($request->to)         $query->where('expense_date', '<=', $request->to);

        $expenses  = $query->orderByDesc('expense_date')->paginate(30);
        $companies = Company::orderBy('name')->get();
        $budgets   = Budget::with('company')->orderByDesc('received_date')->get();
        $projects  = Project::orderBy('name')->get();

        return view('expenses.index', compact('expenses', 'companies', 'budgets', 'projects'));
    }

    public function create(Request $request)
    {
        $budgets   = Budget::with('company')->where('status', 'active')->orderByDesc('received_date')->get();
        $companies = Company::orderBy('name')->get();
        $projects  = Project::with('company')->orderBy('name')->get();
        $tasks     = Task::orderBy('title')->get();
        $selectedBudget = $request->budget_id ? Budget::with('company')->find($request->budget_id) : null;

        $budgetsJson = $budgets->map(fn($b) => [
            'id'           => $b->id,
            'title'        => $b->title,
            'company_id'   => $b->company_id,
            'company_name' => $b->company->name,
            'project_id'   => $b->project_id,
            'currency'     => $b->currency,
            'remaining'    => round($b->remaining(), 2),
        ])->values();

        return view('expenses.create', compact('budgets', 'companies', 'projects', 'tasks', 'selectedBudget', 'budgetsJson'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'budget_id'       => 'nullable|exists:budgets,id',
            'split_budget_id' => 'nullable|exists:budgets,id',
            'company_id'      => 'required|exists:companies,id',
            'title'           => 'required|string|max:255',
            'amount'          => 'required|numeric|min:0.01',
            'expense_date'    => 'required|date',
            'invoice_file'    => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:10240',
        ]);

        $budget      = Budget::findOrFail($request->budget_id);
        $totalAmount = (float) $request->amount;
        $primaryRemaining = $budget->remaining();

        // Handle invoice upload once (shared by both records if split)
        $invoicePath = null;
        $invoiceName = null;
        if ($request->hasFile('invoice_file')) {
            $file        = $request->file('invoice_file');
            $invoicePath = $file->store('invoices', 'public');
            $invoiceName = $file->getClientOriginalName();
        }

        $baseData = array_merge(
            $request->only(['company_id', 'project_id', 'task_id', 'title', 'vendor',
                            'currency', 'expense_date', 'payment_method', 'status', 'notes']),
            ['invoice_file' => $invoicePath, 'invoice_file_name' => $invoiceName]
        );

        // ── Split flow ───────────────────────────────────────────────────────
        if ($totalAmount > $primaryRemaining && $primaryRemaining > 0 && !$request->boolean('allow_over_budget')) {
            if (!$request->split_budget_id) {
                return back()->withInput()->withErrors([
                    'split_budget_id' => 'Please select a secondary budget to fund the overflow of '
                        . fmt_money($totalAmount - $primaryRemaining, $budget->currency) . '.',
                ]);
            }

            $splitBudget    = Budget::findOrFail($request->split_budget_id);
            $overflowAmount = round($totalAmount - $primaryRemaining, 2);

            if ($overflowAmount > $splitBudget->remaining()) {
                return back()->withInput()->withErrors([
                    'split_budget_id' => 'The selected budget only has '
                        . fmt_money($splitBudget->remaining(), $splitBudget->currency)
                        . ' remaining, but needs ' . fmt_money($overflowAmount, $budget->currency) . '.',
                ]);
            }

            // Primary: charged against the original budget up to its remaining
            $primary = Expense::create(array_merge($baseData, [
                'budget_id' => $budget->id,
                'amount'    => round($primaryRemaining, 2),
            ]));

            // Overflow: linked child, charged to the secondary budget
            Expense::create(array_merge($baseData, [
                'budget_id'         => $splitBudget->id,
                'amount'            => $overflowAmount,
                'parent_expense_id' => $primary->id,
            ]));

            return redirect()->route('expenses.index')
                ->with('success', 'Expense split: '
                    . fmt_money($primaryRemaining, $budget->currency) . ' from "' . $budget->title . '" + '
                    . fmt_money($overflowAmount, $splitBudget->currency) . ' from "' . $splitBudget->title . '".');
        }

        // ── Over-budget override (no secondary budget — split into unbudgeted) ─
        if ($totalAmount > $primaryRemaining && $request->boolean('allow_over_budget')) {
            $overflowAmount = round($totalAmount - $primaryRemaining, 2);

            // Primary portion: charges up the remaining budget balance
            $primary = Expense::create(array_merge($baseData, [
                'budget_id' => $budget->id,
                'amount'    => round($primaryRemaining, 2),
            ]));

            // Overflow portion: unbudgeted — no budget_id, linked to primary
            Expense::create(array_merge($baseData, [
                'budget_id'         => null,
                'amount'            => $overflowAmount,
                'parent_expense_id' => $primary->id,
                'notes'             => trim(($baseData['notes'] ?? '') . "\n[Unbudgeted overflow — no secondary budget was available]"),
            ]));

            return redirect()->route('expenses.index')
                ->with('success',
                    fmt_money(round($primaryRemaining, 2), $budget->currency) . ' charged to "' . $budget->title . '". '
                    . fmt_money($overflowAmount, $budget->currency) . ' saved as unbudgeted overflow.');
        }

        // ── Normal flow (no split needed) ────────────────────────────────────
        // If budget is already over (remaining ≤ 0), record the expense anyway —
        // it tracks what the company still owes beyond the original allocation.
        if ($totalAmount > $primaryRemaining && $primaryRemaining > 0) {
            return back()->withInput()->withErrors([
                'amount' => 'This expense (' . fmt_money($totalAmount, $budget->currency)
                    . ') exceeds the budget remaining (' . fmt_money($primaryRemaining, $budget->currency) . ').',
            ]);
        }

        Expense::create(array_merge($baseData, [
            'budget_id' => $budget->id,
            'amount'    => $totalAmount,
        ]));

        return redirect()->route('expenses.index')->with('success', 'Expense recorded.');
    }

    public function show(Expense $expense)
    {
        $expense->load([
            'budget.company', 'company', 'project', 'task',
            'parentExpense.budget', 'overflowExpense.budget',
        ]);
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $budgets   = Budget::with('company')->orderByDesc('received_date')->get();
        $companies = Company::orderBy('name')->get();
        $projects  = Project::with('company')->orderBy('name')->get();
        $tasks     = Task::orderBy('title')->get();
        return view('expenses.edit', compact('expense', 'budgets', 'companies', 'projects', 'tasks'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'budget_id'    => 'required|exists:budgets,id',
            'company_id'   => 'required|exists:companies,id',
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'invoice_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:10240',
        ]);

        $data = $request->only([
            'budget_id', 'company_id', 'project_id', 'task_id',
            'title', 'vendor', 'amount', 'currency', 'expense_date',
            'payment_method', 'status', 'notes',
        ]);

        if ($request->hasFile('invoice_file')) {
            // Delete old file
            if ($expense->invoice_file) {
                Storage::disk('public')->delete($expense->invoice_file);
            }
            $file = $request->file('invoice_file');
            $data['invoice_file']      = $file->store('invoices', 'public');
            $data['invoice_file_name'] = $file->getClientOriginalName();
        }

        if ($request->boolean('remove_file') && $expense->invoice_file) {
            Storage::disk('public')->delete($expense->invoice_file);
            $data['invoice_file']      = null;
            $data['invoice_file_name'] = null;
        }

        $expense->update($data);

        return redirect()->route('expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->invoice_file) {
            Storage::disk('public')->delete($expense->invoice_file);
        }
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted.');
    }
}
