<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }
    public function index()
    {
        $courseByCategory = Course::with('category')
            ->latest()
            ->get()
            ->groupBy(function ($course) {
                return $course->category->name ?? 'Uncategorized';
            });

        return view('front.index', compact('courseByCategory'));
    }

    public function details(Course $course)
    {
        $course->load(['category', 'benefits', 'courseSections.sectionContents']);

        return view('courses.details', compact('course'));
    }

    public function join(Course $course)
    {
        $studentName = $this->courseService->enrollUser($course);
        $firstSectionAndContent = $this->courseService->getFirstSectionAndContent($course);

        return view('course.success_joined', array_merge(
            compact('course', 'studentName'),
        ));
    }

    public function learning(Course $course, $contentId, $sectionContentId)
    {
        $learningData = $this->courseService->getLearningData($course, $contentId, $sectionContentId);

        return view('courses.learning', $learningData);
    }

    public function learning_finished(Course $course)
    {
        return view('courses.learning_finished', compact('course'));
    }

    public function search_courses(Request $request)
    {
        $request->validate([
            'search' => 'required|string',
        ]);

        $keyword = $request->search;
        $courses = Course::where('name', 'like', "%{$keyword}%")
            ->orWhere('about', 'like', "%{$keyword}%")
            ->get();
        return view('courses.search', compact('courses', 'keyword'));
    }
}
