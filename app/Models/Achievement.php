<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = ['title', 'description', 'project_id', 'achieved_date', 'type'];

    protected $casts = ['achieved_date' => 'date'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
