<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'description', 'company_id', 'category_id', 'status', 'start_date', 'end_date'];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }
}
