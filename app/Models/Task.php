<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = ["name", "status", "startDate", "endDate", "project_id", "description"];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
