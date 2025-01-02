<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'name',
        'thumbnail',
        'timelimit',
        'price',
        'discount',
        'tries',
        'category_id',
        'sub_category',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'sub_category');
    }

    public function enrollments()
    {
        return $this->hasMany(QuizEnrollment::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
