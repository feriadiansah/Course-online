@extends('front.layouts.app')
@section('title', 'Success Join - Obito Online Learning Platform')
@section('content')
    <div class="relative flex items-center justify-center h-screen">
        <div id="backgroundImage" class="absolute top-0 left-0 right-0 bottom-0">
            {{-- Ganti h-[777px] menjadi h-full untuk mengisi container --}}
            <img src="{{ asset('assets/images/backgrounds/success-join.png') }}" alt="image"
                class="w-full h-full object-cover" />
        </div>

        {{-- Hapus mt-[178px] karena centering sudah dihandle oleh flexbox parent --}}
        <main class="relative flex flex-col gap-[30px] p-[30px] w-[560px] rounded-[20px] border bg-white border-obito-grey">
            <img src="{{ asset('assets/images/icons/raising-hands.png') }}" alt="icon"
                class="size-[60px] shrink-0 mx-auto" />
            <div class="mx-auto flex w-[500px] flex-col gap-[10px] items-center">
                <h1 class="text-center font-bold text-[28px] leading-[42px]">Welcome to Class,<br>Upgrade Your New Skills
                </h1>
                <p class="text-center text-obito-text-secondary leading-[28px]">Mari kita belajar meningkatkan skills
                    terbaru bersama dengan mentor berpengalaman demi masa depan lebih baik</p>
            </div>
            <div id="card"
                class="flex items-center pt-[10px] pb-[10px] pl-[10px] pr-4 border border-obito-grey rounded-[20px] gap-4">
                <div class="flex justify-center items-center overflow-hidden shrink-0 w-[180px] h-[130px] rounded-[14px]">
                    {{-- Pastikan path ini benar dan php artisan storage:link sudah dijalankan --}}
                    <img src="{{ Storage::url($course->thumbnail) }}" alt="image" class="w-full h-full object-cover" />
                </div>
                <div class="flex flex-col gap-[10px]">
                    <h2 class="font-bold">{{ $course->name }}</h2>
                    <div class="flex items-center gap-[6px]">
                        <img src="{{ asset('assets/images/icons/crown-green.svg') }}" alt="icon"
                            class="size-5 shrink-0" />
                        <p class="text-sm leading-[21px] text-obito-text-secondary">
                            {{ $course->category->name }}
                        </p>
                    </div>
                    <div class="flex items-center gap-[6px]">
                        <img src="{{ asset('assets/images/icons/menu-board-green.svg') }}" alt="icon"
                            class="size-5 shrink-0" />
                        <p class="text-sm leading-[21px] text-obito-text-secondary">{{ $course->content_count }} Lessons</p>
                    </div>
                </div>
            </div>
            <div class="buttons grid grid-cols-2 gap-[12px]">
                <a href="#"
                    class="border border-obito-grey rounded-full py-[10px] flex justify-center items-center hover:border-obito-green transition-all duration-300">
                    <span class="font-semibold">Get Guidelines</span>
                </a>
                <a href="{{ route('dashboard.course.learning', [
                    'course' => $course->slug,
                    'courseSection' => $firstSectionId,
                    'sectionContent' => $firstContentId,
                ]) }}"
                    class="text-white rounded-full py-[10px] flex justify-center items-center bg-obito-green hover:drop-shadow-effect transition-all duration-300">
                    <span class="font-semibold">Start Learning</span>
                </a>
            </div>
        </main>
    </div>

@endsection
