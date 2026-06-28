<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Company;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $query = Budget::with(['company', 'project'])->withCount('expenses');

        if ($request->company_id) $query->where('company_id', $request->company_id);
        if ($request->status)     $query->where('status', $request->status);

        $budgets   = $query->latest('received_date')->paginate(20);
        $companies = Company::orderBy('name')->get();

        // Totals
        $totalReceived = Budget::sum('amount');
        $totalSpent    = \App\Models\Expense::sum('amount');
        $totalRemaining = $totalReceived - $totalSpent;

        return view('budgets.index', compact('budgets', 'companies', 'totalReceived', 'totalSpent', 'totalRemaining'));
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $projects  = Project::with('company')->orderBy('name')->get();
        return view('budgets.create', compact('companies', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id'    => 'required|exists:companies,id',
            'title'         => 'required|string|max:255',
            'amount'        => 'required|numeric|min:0.01',
            'received_date' => 'required|date',
        ]);

        Budget::create($request->only(['company_id', 'project_id', 'title', 'amount', 'currency', 'received_date', 'notes', 'status']));

        return redirect()->route('budgets.index')->with('success', 'Budget recorded successfully.');
    }

    public function show(Budget $budget)
    {
        $budget->load(['company', 'project', 'expenses.project', 'expenses.task']);

        $byProject = $budget->expenses()
            ->join('projects', 'expenses.project_id', '=', 'projects.id')
            ->select('projects.name', DB::raw('SUM(expenses.amount) as total'))
            ->groupBy('projects.id', 'projects.name')
            ->orderByDesc('total')
            ->get();

        $byPaymentMethod = $budget->expenses()
            ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        return view('budgets.show', compact('budget', 'byProject', 'byPaymentMethod'));
    }

    public function edit(Budget $budget)
    {
        $companies = Company::orderBy('name')->get();
        $projects  = Project::with('company')->orderBy('name')->get();
        return view('budgets.edit', compact('budget', 'companies', 'projects'));
    }

    public function update(Request $request, Budget $budget)
    {
        $request->validate([
            'company_id'    => 'required|exists:companies,id',
            'title'         => 'required|string|max:255',
            'amount'        => 'required|numeric|min:0.01',
            'received_date' => 'required|date',
        ]);

        $budget->update($request->only(['company_id', 'project_id', 'title', 'amount', 'currency', 'received_date', 'notes', 'status']));

        return redirect()->route('budgets.show', $budget)->with('success', 'Budget updated.');
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();
        return redirect()->route('budgets.index')->with('success', 'Budget deleted.');
    }
}
