<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = ['company_id', 'project_id', 'title', 'amount', 'currency', 'received_date', 'notes', 'status'];

    protected $casts = ['received_date' => 'date', 'amount' => 'decimal:2'];

    public function company()  { return $this->belongsTo(Company::class); }
    public function project()  { return $this->belongsTo(Project::class); }
    public function expenses() { return $this->hasMany(Expense::class); }

    public function totalSpent(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function remaining(): float
    {
        return (float) $this->amount - $this->totalSpent();
    }

    public function usedPercent(): float
    {
        return $this->amount > 0 ? round($this->totalSpent() / $this->amount * 100, 1) : 0;
    }
}
