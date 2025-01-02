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
        'picture',
        'total_students',
        'courses',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function coursesCount()
    {
        return $this->hasMany(CourseCreator::class, 'user_id', 'user_id');
    }


}
