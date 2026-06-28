<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'budget_id', 'parent_expense_id', 'company_id', 'project_id', 'task_id',
        'title', 'vendor', 'amount', 'currency', 'expense_date',
        'payment_method', 'invoice_file', 'invoice_file_name', 'status', 'notes',
    ];

    protected $casts = ['expense_date' => 'date', 'amount' => 'decimal:2'];

    public function invoiceUrl(): ?string
    {
        return $this->invoice_file ? asset('storage/' . $this->invoice_file) : null;
    }

    public function isImage(): bool
    {
        $ext = strtolower(pathinfo($this->invoice_file_name ?? '', PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    public function budget()          { return $this->belongsTo(Budget::class); }
    public function company()         { return $this->belongsTo(Company::class); }
    public function project()         { return $this->belongsTo(Project::class); }
    public function task()            { return $this->belongsTo(Task::class); }
    public function parentExpense()   { return $this->belongsTo(Expense::class, 'parent_expense_id'); }
    public function overflowExpense() { return $this->hasOne(Expense::class, 'parent_expense_id'); }
}
