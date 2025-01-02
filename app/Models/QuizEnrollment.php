<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizEnrollment extends Model
{
    protected $fillable = [
        'student_id',
        'quiz_id',
        'marks_percentage',
        'remaining_tries',
        'is_active'
    ];


    protected static function booted()
    {
        static::saving(function ($quizEnrollment) {
            if ($quizEnrollment->remaining_tries == 0) {
                $quizEnrollment->is_active = false;
            }
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
