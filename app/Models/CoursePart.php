<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoursePart extends Model
{
    use HasFactory;

    protected $table = 'course_parts';

    protected $fillable = [
        'course_id',
        'name',
        'order'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function courseMaterials()
    {
        return $this->hasMany(CourseMaterial::class, 'part_id');
    }
}
