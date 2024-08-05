<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'about',
        'skills',
        'total_students',
        'courses',
        'reviews',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
