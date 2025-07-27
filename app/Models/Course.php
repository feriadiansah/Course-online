<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'about',
        'is_popular',
        'thumbnail',
        'category_id',
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function category() :BelongsTo
    {
        return $this->belongsTo(category::class);
    }

    public function benefits() :HasMany
    {
        return $this->hasMany(CourseBenefit::class);
    }

    public function courseMentors() :HasMany
    {
        return $this->hasMany(CourseMentor::class, 'course_id');
    }

    public function courseSections() :HasMany
    {
        return $this->hasMany(CourseSection::class, 'course_id');
    }

    public function courseStudents() :HasMany
    {
        return $this->hasMany(CourseStudent::class, 'course_id');
    }

    public function getContentCountAttribute(){
        return $this->courseSections->sum(function ($section){
            return $section->sectionContents->count();
        });
    }
}

