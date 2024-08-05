<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $table = 'course_materials';

    protected $fillable = [
        'part_id',
        'title',
        'type',
        'url',
    ];

    public function coursePart()
    {
        return $this->belongsTo(CoursePart::class, 'part_id');
    }
}
