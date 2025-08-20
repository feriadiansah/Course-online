<?php

namespace App\Services;

use App\Models\Course;
use App\Repositories\CourseRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CourseService
{
    protected $courseRepository;

    public function __construct(CourseRepositoryInterface $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function enrollUser(Course $course)
    {
        $user = Auth::user();

        if (!$course->courseStudents()->where('user_id', $user->id)->exists()) {
            $course->courseStudents()->create([
                'user_id' => $user->id,
                'is_active' => true,
            ]);
        }

        return $user->name;
    }

    public function getFirstSectionAndContent(Course $course)
    {
        $firstSectionId = $course->courseSections()->orderBy('id')->value('id');
        $firstContentId = $firstSectionId
            ? $course->courseSections()->find($firstSectionId)->sectionContents()->orderBy('id')->value('id')
            : null;
        return [
            'firstSectionId' => $firstSectionId,
            'firstContentId' => $firstContentId,
        ];
    }

    // public function getFirstSectionAndContent(Course $course)
    // {
    //     $sections = $course->courseSections()
    //         ->orderBy('id', 'asc')
    //         ->with(['sectionContents' => function ($q) {
    //             $q->orderBy('id', 'asc');
    //         }])
    //         ->get();

    //     $firstSection = $sections->first(fn($s) => $s->sectionContents->isNotEmpty());

    //     return [
    //         'firstSectionId' => $firstSection?->id,
    //         'firstContentId' => $firstSection?->sectionContents->first()?->id,
    //     ];
    // }



    // sebelum
    // public function getLearningData(Course $course, $contentSectionId, $sectionContentId)
    // {
    //   $course->load(['courseSections.sectionContents']);


    //     $currentSection = $course->courseSections->find($contentSectionId);
    //     $currentContent = $currentSection ? $currentSection->sectionContents->find($sectionContentId) : null;

    //     $nextContent = null;

    //     if ($currentContent) {
    //         $nextContent = $currentSection->sectionContents
    //             ->where('id', '>', $currentContent->id)
    //             ->sortBy('id')
    //             ->first();
    //     }

    //     if (!$nextContent && $currentSection) {
    //         $nextSection = $course->courseSections
    //             ->where('id', '>', $currentSection->id)
    //             ->sortBy('id')
    //             ->first();

    //         if ($nextSection) {
    //             $nextContent = $nextSection->sectionContents->sortBy('id')->first();
    //         }
    //     }

    //     return [
    //         'course' => $course,
    //         'currentSection' => $currentSection,
    //         'currentContent' => $currentContent,
    //         'nextContent' => $nextContent,
    //         'isFinished' => !$nextContent,
    //     ];
    // }
    public function getLearningData(Course $course, $contentSectionId, $sectionContentId)
    {
        // ini bener nih
        $course->load([
            'courseSections' => function ($q) {
                $q->orderBy('position', 'asc');
            },
            'courseSections.sectionContents'
        ]);


        $currentSection = $course->courseSections->find($contentSectionId);
        $currentContent = $currentSection
            ? $currentSection->sectionContents->find($sectionContentId)
            : null;

        $nextContent = null;

        if ($currentContent) {
            // cari content berikutnya berdasarkan posisi
            $nextContent = $currentSection->sectionContents
                ->where('position', '>', $currentContent->position)
                ->sortBy('position')
                ->first();
        }

        if (!$nextContent && $currentSection) {
            // cari section berikutnya berdasarkan posisi
            $nextSection = $course->courseSections
                ->where('position', '>', $currentSection->position)
                ->sortBy('position')
                ->first();

            if ($nextSection) {
                $nextContent = $nextSection->sectionContents
                    ->sortBy('position')
                    ->first();
            }
        }

        return [
            'course' => $course,
            'currentSection' => $currentSection,
            'currentContent' => $currentContent,
            'nextContent' => $nextContent,
            'isFinished' => !$nextContent,
        ];
    }


    public function searchCourse($keyword)
    {
        return $this->courseRepository->searchByKeyword($keyword);
    }
    public function getCourseGroupedByCategory()
    {
        $courses = $this->courseRepository->getAllWithCategory();

        return $courses->groupBy(function ($courses) {
            return $courses->category->name ?? 'Uncategorized';
        });
    }
}
