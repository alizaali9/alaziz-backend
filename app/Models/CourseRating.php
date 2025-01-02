<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseRating extends Model
{
    protected $fillable = ['user_id', 'course_id', 'stars'];

    /**
     * Relationship with User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Course model.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
