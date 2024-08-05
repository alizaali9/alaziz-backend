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
        'language',
        'created_by',
        'last_updated',
        'demo_video',
        'price',
        'overview',
        'outcome',
        'requirements',
        'total_lessons',
    ];

    // Define the relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

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

}
