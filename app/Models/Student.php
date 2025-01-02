<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'whatsapp_no',
        'password',
        "picture",
        'immi_number',
        'city',
        'country',
        'roll_no',
        'api_token',
        'token_expires_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($student) {
            $student->enrollments()->delete();

            $student->quizEnrollments()->delete();
        });
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrollments');
    }
    public function quizEnrollments()
    {
        return $this->hasMany(QuizEnrollment::class);
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'quizenrollments');
    }
}
