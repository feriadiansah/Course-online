<?php

namespace App\Models;

use App\Models\CourseSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionContent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'course_section_id',
        'content',
    ];

    public function courseSections(){
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }
}
