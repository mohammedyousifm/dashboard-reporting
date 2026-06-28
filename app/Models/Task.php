<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'description', 'project_id', 'category_id', 'status', 'priority', 'due_date', 'completed_date'];

    protected $casts = ['due_date' => 'date', 'completed_date' => 'date'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
