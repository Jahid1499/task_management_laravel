<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = ["title", "status", "startDate", "endDate", "project_id", "description"];

    public function users()
    {
        return $this->belongsToMany(User::class, 'task_user');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function taskUsers()
    {
        return $this->belongsToMany(User::class, 'task_user', 'task_id', 'user_id');
    }


}
