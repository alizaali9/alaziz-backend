<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Category extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name',
    ];
    public function courses()
    {
        return $this->hasMany(Course::class, 'course_category');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'category_id');
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
}