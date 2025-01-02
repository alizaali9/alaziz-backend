<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'level',
        'no_of_raters',
        'course_stars',
        'course_category',
        'sub_category',
        'thumbnail',
        'language',
        'created_by',
        'last_updated',
        'demo_video',
        'price',
        'discount',
        'overview',
        'outcome',
        'requirements',
        'total_lessons',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'course_category');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'sub_category');
    }

    public function courseParts()
    {
        return $this->hasMany(CoursePart::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments');
    }

    public function creators()
    {
        return $this->belongsToMany(User::class, 'course_creators', 'course_id', 'user_id')
                    ->withTimestamps();
    }

}
