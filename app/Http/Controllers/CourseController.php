<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CoursePart;
use App\Models\CourseCreator;
use App\Models\CourseRating;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{

    public function index()
    {
        $categories = Subcategory::all();
        $users = User::where('role', 2)->get();
        return view('content.courses.create', compact('categories', 'users'));
    }
    public function showCourses(Request $request)
    {
        $search = $request->input('search');
        $courses = Course::with('subcategory.category')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('subcategory', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->get();
        $categories = Subcategory::all();
        $users = User::where('role', 2)->get();
        return view('content.courses.manage', compact('courses', 'categories', 'users'));
    }

    public function downloadCsv()
    {
        $courses = Course::with(['subcategory.category'])->get();

        $headers = [
            'Course Name',
            'Description',
            'Category',
            'Subcategory',
            'Price',
            'Discount',
            'Requirements',
            'Level',
            'Overview',
            'Outcome',
            'Thumbnail Path',
            'Demo Path'
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'courses_');

        $file = fopen($tempFile, 'w');

        fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        fputcsv($file, $headers);

        foreach ($courses as $course) {
            $isYouTube = preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\//', $course->demo_video) ? true : false;
            $videoUrl = $isYouTube
                ? $course->demo_video
                : ($course->demo_video
                    ? asset('storage/' . $course->demo_video)
                    : null);

            fputcsv($file, [
                $course->name,
                $course->description,
                optional($course->subcategory->category)->name,
                optional($course->subcategory)->name,
                $course->price,
                $course->discount,
                $course->requirements,
                $course->level,
                $course->overview,
                $course->outcome,
                $course->thumbnail ? asset('storage/' . $course->thumbnail) : null,
                $videoUrl
            ]);
        }

        fclose($file);

        $filename = 'courses_' . date('Y-m-d_H-i-s') . '.csv';

        $response = response()->stream(function () use ($tempFile) {
            readfile($tempFile);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);

        unlink($tempFile);

        return $response;
    }


    public function getCourseParts($courseId, Request $request)
    {
        try {
            $course = Course::findOrFail($courseId);

            $query = $request->input('search');
            $parts = CoursePart::where('course_id', $courseId)
                ->when($query, function ($q) use ($query) {
                    return $q->where('name', 'LIKE', '%' . $query . '%');
                })
                ->orderBy('order')
                ->get();

            return view('content.courses.manage-parts', compact('parts', 'course'));
        } catch (\Exception $e) {
            Log::error('Error retrieving course parts: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue retrieving the course parts. Please try again.');
        }
    }


    public function downloadPartsCsv(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        $query = CoursePart::where('course_id', $courseId);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $parts = $query->get();

        $csvData = "Part Name\n";
        foreach ($parts as $part) {
            $csvData .= $part->name . "\n";
        }

        $fileName = 'course_parts_' . $course->name . '.csv';

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$fileName\"");
    }



    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50',
                'language' => 'required|string|max:50',
                'overview' => 'nullable|string',
                'description' => 'required|string',
                'level' => 'required|string|in:beginner,intermediate,advance',
                'sub_category' => 'required|exists:subcategories,id',
                'price' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'outcome' => 'nullable|string',
                'requirements' => 'nullable|string',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg',
                'demo' => 'nullable|mimes:mp4,avi,mkv|max:102400',
                'url' => 'nullable|string',
                'instructors' => 'nullable|exists:users,id',
                'instructors.*' => 'nullable|exists:users,id',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $course = Course::findOrFail($id);

            $course->name = $request->input('name');
            $course->language = $request->input('language');
            $course->overview = $request->input('overview');
            $course->description = $request->input('description');
            $course->level = $request->input('level');
            $course->sub_category = $request->input('sub_category');
            $category = Subcategory::find($request->input('sub_category'));
            $course->course_category = $category->category_id;
            $course->price = $request->input('price');
            $course->discount = $request->input('discount');
            $course->outcome = $request->input('outcome');
            $course->requirements = $request->input('requirements');
            $instructorIds = $request->input('instructors', []);

            if (count($instructorIds) > 0) {
                $course->creators()->sync($instructorIds);
            }


            if ($request->hasFile('thumbnail')) {
                if ($course->thumbnail) {
                    Storage::delete('public/' . $course->thumbnail);
                }
                $course->thumbnail = $request->file('thumbnail')->store('thumbnails', 'public');
            }

            if ($request->hasFile('demo')) {
                if ($course->demo_video) {
                    Storage::delete('public/' . $course->demo_video);
                }
                $course->demo_video = $request->file('demo')->store('demo_videos', 'public');
            } else {
                $course->demo_video = $request->input('url');
            }

            $course->save();

            return redirect()->back()->with('success', 'Course updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating course: ' . $e->getMessage());

            return redirect()->back()->with('error', 'There was an error updating the course. Please try again.');
        }
    }

    public function createParts($courseid)
    {
        return view('content.courses.create-parts', compact('courseid'));
    }

    public function uploadContent($courseid)
    {
        $courseParts = CoursePart::where('course_id', $courseid)->get();
        return view('content.courses.upload-course-content', compact('courseid', 'courseParts'));
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:100',
            'level' => 'required|string',
            'language' => 'required|string',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'sub_category' => 'required|integer|exists:subcategories,id',
            'demo' => 'nullable|file|mimes:mp4,avi,mkv|max:102400',
            'url' => 'nullable|string',
            'thumbnail' => 'required|file|mimes:jpg,jpeg,png',
            'overview' => 'nullable|string',
            'outcome' => 'nullable|string',
            'requirement' => 'nullable|string',
            'instructors' => 'nullable|exists:users,id',
            'instructors.*' => 'nullable|exists:users,id',
        ]);


        if ($validation->fails()) {
            return redirect()->back()
                ->withErrors($validation)
                ->withInput();
        }

        try {
            $subcategory = Subcategory::find($request->sub_category);
            $category = $subcategory->category;
            $course = new Course();
            $course->name = $request->name;
            $course->description = $request->description;
            $course->level = $request->level;
            $course->course_category = $category->id;
            $course->sub_category = $request->sub_category;
            $course->language = $request->language;
            $course->price = $request->price;
            $course->discount = $request->discount ? $request->discount : 0;
            $course->overview = $request->overview;
            $course->outcome = $request->outcome;
            $course->requirements = $request->requirement;
            $instructorIds = $request->input('instructors', []);

            if ($request->hasFile('demo')) {
                $demoPath = $request->file('demo')->store('demo_videos', 'public');
                $course->demo_video = $demoPath;
            } else {
                $course->demo_video = $request->input('url');
            }

            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
                $course->thumbnail = $thumbnailPath;
            }

            $course->save();
            if (count($instructorIds) > 0) {
                foreach ($instructorIds as $instructorId) {
                    CourseCreator::create([
                        'course_id' => $course->id,
                        'user_id' => $instructorId,
                    ]);
                }
            } else {
                $instructor = CourseCreator::create([
                    'course_id' => $course->id,
                    'user_id' => Auth::id(),
                ]);
            }



            return redirect()->route('courses.createParts', ['courseid' => $course->id])
                ->with('success', 'Course created successfully. Now add course parts.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'There was an issue creating the course. Please try again.']);
        }
    }



    public function storeParts(Request $request)
    {
        $course_id = $request->input('course_id');

        $courseValidation = Validator::make($request->all(), [
            'course_id' => 'required|integer|exists:courses,id',
            'name' => 'required|array',
            'name.*' => 'required|string|max:50'
        ]);

        if ($courseValidation->fails()) {
            return redirect()->back()
                ->withErrors($courseValidation)
                ->withInput();
        }

        foreach ($request->name as $index => $partName) {
            $partValidation = Validator::make(
                ['name' => $partName],
                [
                    'name' => [
                        'required',
                        'string',
                        'max:50',
                        Rule::unique('course_parts')->where(function ($query) use ($course_id) {
                            return $query->where('course_id', $course_id);
                        })
                    ]
                ]
            );

            if ($partValidation->fails()) {
                return redirect()->back()
                    ->withErrors(["name.{$index}" => "The name '{$partName}' is already taken for this course."])
                    ->withInput();
            }
        }

        try {
            $currentMaxOrder = CoursePart::where('course_id', $course_id)->max('order') ?? 0;

            foreach ($request->name as $partName) {
                $currentMaxOrder++;
                CoursePart::create([
                    'course_id' => $course_id,
                    'name' => $partName,
                    'order' => $currentMaxOrder,
                ]);
            }

            return redirect()->route('courses.uploadContent', $course_id)
                ->with('success', 'Course parts added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'There was an issue adding the course parts. Please try again.']);
        }
    }

    public function moveUpPart($id)
    {
        $part = CoursePart::find($id);
        if (!$part) {
            return redirect()->back()->withErrors(['error' => 'Lesson Part not found.']);
        }

        $previousPart = CoursePart::where('course_id', $part->course_id)
            ->where('order', '<', $part->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($previousPart) {
            $tempOrder = $part->order;
            $part->order = $previousPart->order;
            $previousPart->order = $tempOrder;

            $part->save();
            $previousPart->save();

            return redirect()->back()->with('success', 'Lesson Part moved up successfully.');
        }

        return redirect()->back()->withErrors(['error' => 'Cannot move the lesson part up.']);
    }


    public function moveDownPart($id)
    {
        $part = CoursePart::find($id);
        if (!$part) {
            return redirect()->back()->withErrors(['error' => 'Lesson Part not found.']);
        }

        $nextPart = CoursePart::where('course_id', $part->course_id)
            ->where('order', '>', $part->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($nextPart) {
            $tempOrder = $part->order;
            $part->order = $nextPart->order;
            $nextPart->order = $tempOrder;

            $part->save();
            $nextPart->save();

            return redirect()->back()->with('success', 'Lesson Part moved down successfully.');
        }

        return redirect()->back()->withErrors(['error' => 'Cannot move the Lesson part down.']);
    }


    // app/Http/Controllers/CourseController.php


    public function deleteCourse($id)
    {
        try {
            $course = Course::findOrFail($id);

            foreach ($course->courseParts as $part) {
                CourseMaterial::where('part_id', $part->id)->delete();
                $part->delete();
            }

            $course->delete();

            return redirect()->back()->with('success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting course: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the course. Please try again.');
        }
    }


    public function updatePart(Request $request, $id)
    {
        $coursePart = CoursePart::findOrFail($id);

        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('course_parts')->where(function ($query) use ($request, $coursePart) {
                    return $query->where('course_id', $coursePart->course_id);
                })->ignore($id),
            ],
            'course_id' => 'required|integer|exists:courses,id',
        ];

        $messages = [
            'name.required' => 'The part name is required.',
            'name.string' => 'The part name must be a string.',
            'name.max' => 'The part name may not be greater than :max characters.',
            'name.unique' => 'The part name ":input" has already been taken within this course.',
            'course_id.required' => 'The course ID is required.',
            'course_id.integer' => 'The course ID must be an integer.',
            'course_id.exists' => 'The selected course ID is invalid.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $coursePart->name = $request->input('name');
            $coursePart->save();

            return redirect()->back()->with('success', 'Course part updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating course part: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue updating the course part. Please try again.');
        }
    }


    public function deleteCoursePart($id)
    {
        try {
            $coursePart = CoursePart::findOrFail($id);

            if ($coursePart) {
                CourseMaterial::where('part_id', $id)->delete();

                $coursePart->delete();

                return redirect()->back()->with('success', 'Course part deleted successfully.');
            }

            return redirect()->back()->with('error', 'Course part not found.');
        } catch (\Exception $e) {
            Log::error('Error deleting course part: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the course part. Please try again.');
        }
    }

    public function manageLessons($courseId)
    {
        try {
            $course = Course::findOrFail($courseId);

            $courseParts = CoursePart::where('course_id', $courseId)
                ->orderBy('order', 'asc')
                ->get();

            $query = CourseMaterial::whereIn('part_id', $courseParts->pluck('id'))
                ->with([
                    'coursePart' => function ($query) {
                        $query->orderBy('order', 'asc');
                    }
                ])
                ->orderBy('part_id')
                ->orderBy('order');

            if (request()->has('search')) {
                $search = request()->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhereHas('coursePart', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        })
                        ->orWhere('type', 'LIKE', "%{$search}%");
                });
            }

            $lessons = $query->get();

            return view('content.courses.manage-lessons', compact('course', 'lessons', 'courseParts'));
        } catch (\Exception $e) {
            Log::error('Error retrieving lessons: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue retrieving the lessons. Please try again.');
        }
    }


    public function moveUp($materialId)
    {
        try {
            $material = CourseMaterial::findOrFail($materialId);
            $previousMaterial = CourseMaterial::where('part_id', $material->part_id)
                ->where('order', '<', $material->order)
                ->orderBy('order', 'desc')
                ->first();

            if ($previousMaterial) {
                DB::transaction(function () use ($material, $previousMaterial) {
                    $tempOrder = $material->order;
                    $material->order = $previousMaterial->order;
                    $previousMaterial->order = $tempOrder;

                    $material->save();
                    $previousMaterial->save();
                });

                return redirect()->back()->with('success', 'Material moved up successfully.');
            }

            return redirect()->back()->with('error', 'No previous material to move up.');
        } catch (\Exception $e) {
            Log::error('Error moving material up: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue moving the material up. Please try again.');
        }
    }

    public function moveDown($materialId)
    {
        try {
            $material = CourseMaterial::findOrFail($materialId);
            $nextMaterial = CourseMaterial::where('part_id', $material->part_id)
                ->where('order', '>', $material->order)
                ->orderBy('order', 'asc')
                ->first();

            if ($nextMaterial) {
                DB::transaction(function () use ($material, $nextMaterial) {
                    $tempOrder = $material->order;
                    $material->order = $nextMaterial->order;
                    $nextMaterial->order = $tempOrder;

                    $material->save();
                    $nextMaterial->save();
                });

                return redirect()->back()->with('success', 'Material moved down successfully.');
            }

            return redirect()->back()->with('error', 'No next material to move down.');
        } catch (\Exception $e) {
            Log::error('Error moving material down: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue moving the material down. Please try again.');
        }
    }



    public function storeContent(Request $request)
    {
        $rules = [
            'course_id' => 'required|integer|exists:courses,id',
            'part' => 'required|integer|exists:course_parts,id',
            'title' => 'required|string|max:255',
            'lesson_file' => 'nullable|file|mimes:mp4,avi,mkv,pdf',
            'lesson_url' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->file('lesson_file') == null && $request->input('lesson_url') == null) {
            return redirect()->back()->with('error', 'It is must to upload lesson');
        }

        DB::beginTransaction();

        try {
            $lessonPath = null;
            $type = null;

            if ($request->hasFile('lesson_file')) {
                $lessonPath = $request->file('lesson_file')->store('course_lessons', 'public');
                $fileType = $request->file('lesson_file')->getClientOriginalExtension();
                $type = in_array($fileType, ['mp4', 'avi', 'mkv']) ? 'video' : 'pdf';
            } else {
                $lessonPath = $request->lesson_url;
                $type = 'url';
            }

            $lastMaterial = CourseMaterial::where('part_id', $request->part)
                ->orderBy('order', 'desc')
                ->first();

            $nextOrder = $lastMaterial ? $lastMaterial->order + 1 : 1;

            $material = CourseMaterial::create([
                'part_id' => $request->part,
                'title' => $request->title,
                'type' => $type,
                'url' => $lessonPath,
                'order' => $nextOrder,
            ]);
            // dd($material);

            DB::commit();

            return redirect()->back()->with('success', 'Lesson uploaded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error uploading lesson: ' . $e->getMessage());

            return redirect()->back()->withErrors(['error' => 'There was an issue uploading the lesson. Please try again.']);
        }
    }

    public function getAllCourses()
    {
        $courses = Course::with(['courseParts.courseMaterials'])->get();

        $courses->transform(function ($course) {

            $course->thumbnail = $course->thumbnail ? asset('storage/' . $course->thumbnail) : null;

            $course->enrolled_students = $course->students()->count();

            foreach ($course->courseParts as $part) {
                foreach ($part->courseMaterials as $material) {
                    if ($material->type != "url") {
                        $material->url = asset('storage/' . $material->url);
                    }
                }
            }

            return $course;
        });

        return response()->json($courses);
    }


    public function downloadLessonsCSV($courseId)
    {
        try {
            $course = Course::findOrFail($courseId);
            $courseParts = CoursePart::where('course_id', $courseId)->get();

            $query = CourseMaterial::whereIn('part_id', $courseParts->pluck('id'))->with('coursePart');

            if (request()->has('search')) {
                $search = request()->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhereHas('coursePart', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        })
                        ->orWhere('type', 'LIKE', "%{$search}%");
                });
            }

            $lessons = $query->get();

            $csvData = [];

            $csvData[] = ['Lesson Name', 'Lesson Type', 'Lesson Part', 'Lesson URL'];

            foreach ($lessons as $lesson) {
                $isYouTube = preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\//', $lesson->url) ? true : false;
                $lessonUrl = $isYouTube
                    ? $lesson->url
                    : ($lesson->url
                        ? asset('storage/' . $lesson->url)
                        : null);
                $csvData[] = [
                    $lesson->title,
                    $lesson->type,
                    $lesson->coursePart->name,
                    $lessonUrl
                ];
            }

            $filename = "lessons_of_{$course->name}.csv";
            $handle = fopen($filename, 'w+');
            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);

            return response()->download($filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Error downloading lessons CSV: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue downloading the CSV. Please try again.');
        }
    }


    public function updateLesson(Request $request, $lessonId)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'lesson' => 'nullable|file|mimes:mp4,avi,mkv,pdf',
            'lesson_url' => 'nullable|string',
            'part' => 'required|integer|exists:course_parts,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $courseMaterial = CourseMaterial::findOrFail($lessonId);

            $courseMaterial->title = $request->input('title');
            $courseMaterial->part_id = $request->input('part');
            $type = $courseMaterial->type;

            if ($request->hasFile('lesson')) {
                if ($courseMaterial->url) {
                    Storage::delete($courseMaterial->url);
                }
                $lessonPath = $request->file('lesson')->store('course_lessons', 'public');
                $fileType = $request->file('lesson')->getClientOriginalExtension();
                $type = in_array($fileType, ['mp4', 'avi', 'mkv']) ? 'video' : 'pdf';
                $courseMaterial->url = $lessonPath;
            } else {
                if ($request->input('lesson_url')) {
                    $courseMaterial->url = $request->input('lesson_url');
                    $type = 'url';
                }
            }

            $courseMaterial->type = $type;
            $courseMaterial->save();

            DB::commit();

            return redirect()->back()->with('success', 'Lesson updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating lesson: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'There was an issue updating the lesson. Please try again.']);
        }
    }

    public function deleteLesson($lessonId)
    {
        try {
            $courseMaterial = CourseMaterial::findOrFail($lessonId);

            if ($courseMaterial->url) {
                Storage::delete($courseMaterial->url);
            }

            $courseMaterial->delete();

            return redirect()->back()->with('success', 'Lesson deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting lesson: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the lesson. Please try again.');
        }
    }
    public function getCourseDetails($id)
    {
        $token = request()->header('Authorization');
        $student = null;
        if ($token) {
            $student = Student::where('api_token', $token)->first();
        }

        $course = Course::with([
            'courseParts' => function ($query) {
                $query->orderBy('order', 'asc');
            },
            'courseParts.courseMaterials' => function ($query) {
                $query->orderBy('order', 'asc');
            }
        ])->find($id);

        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        $course->thumbnail = $course->thumbnail ? asset('storage/' . $course->thumbnail) : null;
        $course->demo_video = $course->demo_video && filter_var($course->demo_video, FILTER_VALIDATE_URL)
            ? $course->demo_video
            : ($course->demo_video ? asset('storage/' . $course->demo_video) : null);

        $subcategory = Subcategory::find($course->sub_category);
        $category = Category::find($course->category);

        $course->course_category = $category;
        $course->sub_category = $subcategory;


        $courseRating = CourseRating::where('user_id', $student->id)
            ->where('course_id', $id)
            ->first();

        if ($courseRating) {
            $stars = $courseRating->stars;
            $course->isRated = true;
            $course->stars = $stars;
        } else {
            $course->isRated = false;
        }

        $course->enrolled_students = $course->students()->count();
        $creators = $course->creators->map(function ($creator) {
            if ($creator->role == 2) {
                $instructor = Instructor::where('user_id', $creator->id)->first();
                if ($instructor) {
                    $instructor->picture = $instructor->picture ? asset('storage/' . $instructor->picture) : null;
                    $creator->instructor = $instructor;
                }
            }
            return $creator;
        });

        $course->creators = $creators;


        foreach ($course->courseParts as $part) {
            foreach ($part->courseMaterials as $material) {
                if ($material->type != "url") {
                    $material->url = asset('storage/' . $material->url);
                }
            }
        }

        return response()->json($course);
    }



    public function updateRatings(Request $request, $courseId)
    {
        $validation = Validator::make($request->all(), [
            'stars' => 'required|numeric|min:0|max:5',
            'roll_no' => 'required|exists:students,roll_no',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        }

        $student = Student::where('roll_no', $request->roll_no)->first();
        if (!$student) {
            return response()->json([
                'error' => 'Student not found with the given roll number.',
            ], 404);
        }

        try {
            $courseRating = CourseRating::updateOrCreate(
                [
                    'user_id' => $student->id,
                    'course_id' => $courseId,
                ],
                [
                    'stars' => $request->stars,
                ]
            );

            $course = Course::find($courseId);

            $noOfRaters = CourseRating::where('course_id', $courseId)->count();

            $course->no_of_raters = $noOfRaters;

            $totalStars = CourseRating::where('course_id', $courseId)->sum('stars');
            $course->course_stars = $noOfRaters > 0
                ? number_format($totalStars / $noOfRaters, 2, '.', '')
                : 0;

            $course->save();

            return response()->json([
                'message' => $courseRating->wasRecentlyCreated
                    ? 'Course rating created successfully.'
                    : 'Course rating updated successfully.',
                'course_stars' => $course->course_stars,
                'no_of_raters' => $course->no_of_raters,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'error' => 'There was an issue updating the course ratings. Please try again.'], 500);
        }
    }
}
