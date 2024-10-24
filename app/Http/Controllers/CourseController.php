<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CoursePart;
use App\Models\Instructor;
use App\Models\Subcategory;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
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
        return view('content.courses.create', compact('categories'));
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
        return view('content.courses.manage', compact('courses', 'categories'));
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
            $isURL = preg_match('/^(https?:\/\/)?([a-z0-9\-]+\.)+[a-z]{2,}/i', $course->demo_video) ? true : false;
            $videoUrl = $isURL
                ? $course->demo_video
                : ($course->demo_video
                    ? Storage::disk('google')->url($course->demo_video)
                    : null);

            fputcsv($file, [
                $course->name,
                $course->description,
                optional($course->subcategory->category)->name,
                optional($course->subcategory)->name,
                $course->price,
                $course->requirements,
                $course->level,
                $course->overview,
                $course->outcome,
                $course->thumbnail ? Storage::disk('google')->url($course->thumbnail)  : null,
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
                'outcome' => 'nullable|string',
                'requirements' => 'nullable|string',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'demo' => 'nullable|mimes:mp4,avi,mkv|max:10240',
                'url' => 'nullable|string'
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
            $course->price = $request->input('price');
            $course->outcome = $request->input('outcome');
            $course->requirements = $request->input('requirements');

            if ($request->hasFile('thumbnail')) {
                if ($course->thumbnail) {
                    Storage::disk('google')->delete($course->thumbnail);
                }
                $thumbnail = $request->file('thumbnail');

                $extension = $thumbnail->getClientOriginalExtension();
                $fileData = File::get($thumbnail);
                $thumbnailPath = env('GOOGLE_DERIVE_FOLDER_NAME') . '/course_thumbnails/thumbnail' . uniqid() . '.' . $extension;
                Storage::disk('google')->put($thumbnailPath, $fileData);
                Storage::disk('google')->setVisibility($thumbnailPath, 'public');
            }

            if ($request->hasFile('demo')) {
                if ($course->demo_video) {
                    Storage::disk('google')->delete($course->demo_video);
                }
                $demoFile = $request->file('demo');

                $extension = $demoFile->getClientOriginalExtension();
                $fileData = File::get($demoFile);
                $uniqueFileName = env('GOOGLE_DERIVE_FOLDER_NAME') . '/demo_videos/demo_' . uniqid() . '.' . $extension;
                Storage::disk('google')->put($uniqueFileName, $fileData);
                Storage::disk('google')->setVisibility($uniqueFileName, 'public');
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
            'description' => 'required|string',
            'level' => 'required|string',
            'language' => 'required|string',
            'price' => 'required|numeric',
            'sub_category' => 'required|integer|exists:subcategories,id',
            'demo' => 'nullable|file|mimes:mp4,avi,mkv|max:20480',
            'url' => 'nullable|string',
            'thumbnail' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'overview' => 'nullable|string',
            'outcome' => 'nullable|string',
            'requirement' => 'nullable|string',
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
            $course->created_by = Auth::id();
            $course->language = $request->language;
            $course->price = $request->price;
            $course->overview = $request->overview;
            $course->outcome = $request->outcome;
            $course->requirements = $request->requirement;

            if ($request->hasFile('demo')) {
                $demoFile = $request->file('demo');

                $extension = $demoFile->getClientOriginalExtension();
                $fileData = File::get($demoFile);
                $uniqueFileName = env('GOOGLE_DERIVE_FOLDER_NAME') . '/demo_videos/demo_' . uniqid() . '.' . $extension;
                Storage::disk('google')->put($uniqueFileName, $fileData);
                Storage::disk('google')->setVisibility($uniqueFileName, 'public');
                $course->demo_video = $uniqueFileName;
            } else {
                $course->demo_video = $request->input('url');
            }

            if ($request->hasFile('thumbnail')) {
                $thumbnail = $request->file('thumbnail');

                $extension = $thumbnail->getClientOriginalExtension();
                $fileData = File::get($thumbnail);
                $thumbnailPath = env('GOOGLE_DERIVE_FOLDER_NAME') . '/course_thumbnails/thumbnail_' . uniqid() . '.' . $extension;
                Storage::disk('google')->put($thumbnailPath, $fileData);
                Storage::disk('google')->setVisibility($thumbnailPath, 'public');
                $course->thumbnail = $thumbnailPath;
            }

            $course->save();

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
            foreach ($request->name as $partName) {
                CoursePart::create([
                    'course_id' => $course_id,
                    'name' => $partName,
                ]);
            }

            return redirect()->route('courses.uploadContent', $course_id)
                ->with('success', 'Course parts added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'There was an issue adding the course parts. Please try again.']);
        }
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



    public function storeContent(Request $request)
    {
        // $file = $request->file('lesson_file');

        // dd($file);

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
                $lesson = $request->file('lesson_file');

                $extension = $lesson->getClientOriginalExtension();
                $fileData = File::get($lesson);
                $lessonPath = env('GOOGLE_DERIVE_FOLDER_NAME') . '/course_lessons/lesson_' . uniqid() . '.' . $extension;
                Storage::disk('google')->put($lessonPath, $fileData);
                Storage::disk('google')->setVisibility($lessonPath, 'public');
                $fileType = $request->file('lesson_file')->getClientOriginalExtension();
                $type = in_array($fileType, ['mp4', 'avi', 'mkv']) ? 'video' : 'pdf';
            } else {
                $lessonPath = $request->lesson_url;
                $type = 'url';
            }

            $material = CourseMaterial::create([
                'part_id' => $request->part,
                'title' => $request->title,
                'type' => $type,
                'url' => $lessonPath,
            ]);

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

            $course->thumbnail = $course->thumbnail ? Storage::disk('google')->url($course->thumbnail) : null;

            $course->enrolled_students = $course->students()->count();

            foreach ($course->courseParts as $part) {
                foreach ($part->courseMaterials as $material) {
                    if ($material->type != "url") {
                        $material->url = Storage::disk('google')->url($material->url);
                    }
                }
            }

            return $course;
        });

        return response()->json($courses);
    }


    public function manageLessons($courseId)
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

            return view('content.courses.manage-lessons', compact('course', 'lessons', 'courseParts'));
        } catch (\Exception $e) {
            Log::error('Error retrieving lessons: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue retrieving the lessons. Please try again.');
        }
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
                $isURL = preg_match('/^(https?:\/\/)?([a-z0-9\-]+\.)+[a-z]{2,}/i', $lesson->url) ? true : false;
                $lessonUrl = $isURL
                    ? $lesson->url
                    : ($lesson->url
                        ? Storage::disk('google')->url($lesson->url)
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
            'lesson' => 'nullable|file|mimes:mp4,avi,mkv,pdf|max:20480',
            'lesson_url' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $courseMaterial = CourseMaterial::findOrFail($lessonId);

            $courseMaterial->title = $request->input('title');
            $type = $courseMaterial->type;

            if ($request->hasFile('lesson')) {
                if ($courseMaterial->url) {
                    Storage::disk('google')->delete($courseMaterial->url);
                }
                $lesson = $request->file('lesson');

                $extension = $lesson->getClientOriginalExtension();
                $fileData = File::get($lesson);
                $lessonPath = env('GOOGLE_DERIVE_FOLDER_NAME') . '/course_lessons/lesson_' . uniqid() . '.' . $extension;
                Storage::disk('google')->put($lessonPath, $fileData);
                Storage::disk('google')->setVisibility($lessonPath, 'public');
                $fileType = $request->file('lesson')->getClientOriginalExtension();
                $type = in_array($fileType, ['mp4', 'avi', 'mkv']) ? 'video' : 'pdf';
                $courseMaterial->url = $lessonPath;
            } else {
                $courseMaterial->url = $request->input('lesson_url');
                $type = 'url';
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
                Storage::disk('google')->delete($courseMaterial->url);
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
        $course = Course::with(['courseParts.courseMaterials'])->find($id);

        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        $course->thumbnail = $course->thumbnail ? Storage::disk('google')->url($course->thumbnail) : null;
        $course->demo_video = $course->demo_video && filter_var($course->demo_video, FILTER_VALIDATE_URL)
            ? $course->demo_video
            : ($course->demo_video ? Storage::disk('google')->url($course->demo_video) : null);

        $subcategory = Subcategory::find($course->sub_category);
        $category = Category::find($course->category);

        $course->course_category = $category;
        $course->sub_category = $subcategory;

        $course->enrolled_students = $course->students()->count();

        if ($course->creator->role == 2) {
            $instructor = Instructor::where('user_id', $course->creator->id)->first();
            $course->instructor = $instructor->picture ? Storage::disk('google')->url($instructor->picture): null;
        } else {
            $course->instructor = null;
        }

        foreach ($course->courseParts as $part) {
            foreach ($part->courseMaterials as $material) {
                if ($material->type != "url") {
                    $material->url = Storage::disk('google')->url($material->url);
                }
            }
        }
        return response()->json($course)
        ->withHeaders([
            'Content-Security-Policy' => "default-src 'none'; media-src 'self' https://drive.usercontent.google.com;"
        ]);

    }
    public function fetchVideo($id)
    {
        $videoUrl = 'https://drive.google.com/uc?export=download&id=' . $id;

        $response = Http::get($videoUrl);

        return response($response->body(), $response->status())
            ->header('Content-Type', 'video/mp4');
    }



    public function updateRatings(Request $request, $courseId)
    {
        $validation = Validator::make($request->all(), [
            'no_of_raters' => 'required|integer|min:0',
            'course_stars' => 'required|numeric|min:0|max:5',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        }

        try {
            $course = Course::findOrFail($courseId);
            $course->no_of_raters = $request->input('no_of_raters');
            $course->course_stars = $request->input('course_stars');
            $course->save();

            return response()->json(['status' => 200, 'message' => 'Course ratings updated successfully', 'course' => $course], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => 'There was an issue updating the course ratings. Please try again.'], 500);
        }
    }

}
