<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CoursePart;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{

    public function index()
    {
        $categories = Subcategory::all();
        return view('content.courses.create', compact('categories'));
    }
    public function showCourses()
    {
        $courses = Course::all();
        $categories = Subcategory::all();
        return view('content.courses.manage', compact('courses', 'categories'));
    }

    public function getCourseParts($courseId)
    {
        try {
            $parts = CoursePart::where('course_id', $courseId)->get();
            $course = Course::findOrFail($courseId);
            return view('content.courses.manage-parts', compact('parts', 'course'));
        } catch (\Exception $e) {
            Log::error('Error retrieving course parts: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue retrieving the course parts. Please try again.');
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
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
                    Storage::delete($course->thumbnail);
                }
                $course->thumbnail = $request->file('thumbnail')->store('thumbnails');
            }

            if ($request->hasFile('demo')) {
                if ($course->demo_video) {
                    Storage::delete($course->demo_video);
                }
                $course->demo_video = $request->file('demo')->store('demo_videos');
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
            'name' => 'required|string|max:255|unique:courses,name',
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
                $demoPath = $request->file('demo')->store('demo_videos', 'public');
                $course->demo_video = $demoPath;
            } else {
                $course->demo_video = $request->input('url');
            }

            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
                $course->thumbnail = $thumbnailPath;
            }

            // dd( $course);
            $course->save();

            return redirect()->route('courses.createParts', ['courseid' => $course->id])
                ->with('success', 'Course created successfully. Now add course parts.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => 'There was an issue creating the course. Please try again.']);
        }

    }


    public function storeParts(Request $request)
    {
        $rules = [
            'course_id' => 'required|integer|exists:courses,id',
            'name.*' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    $courseId = $request->course_id;
                    if (CoursePart::where('course_id', $courseId)->where('name', $value)->exists()) {
                        $fail("The course part name \"$value\" has already been taken for this course.");
                    }
                },
            ],
        ];

        $messages = [
            'name.*.required' => 'Each course part name is required.',
            'name.*.string' => 'Each course part name must be a string.',
            'name.*.max' => 'Each course part name may not be greater than :max characters.',
        ];

        $request->validate($rules, $messages);

        $courseId = $request->course_id;
        $names = $request->name;

        try {
            foreach ($names as $name) {
                CoursePart::create([
                    'course_id' => $courseId,
                    'name' => $name,
                ]);
            }

            return redirect()->route('courses.uploadContent', ['courseid' => $courseId])
                ->with('success', 'Course parts created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while creating course parts. Please try again.');
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
        $rules = [
            'name' => 'required|string|max:255|unique:course_parts,name,' . $id,
            'course_id' => 'required|integer|exists:courses,id',
        ];

        $messages = [
            'name.required' => 'The part name is required.',
            'name.string' => 'The part name must be a string.',
            'name.max' => 'The part name may not be greater than :max characters.',
            'name.unique' => 'The part name ":input" has already been taken.',
            'course_id.required' => 'The course ID is required.',
            'course_id.integer' => 'The course ID must be an integer.',
            'course_id.exists' => 'The selected course ID is invalid.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        // dd($validator->fails());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }



        try {
            $coursePart = CoursePart::findOrFail($id);

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
        $rules = [
            'course_id' => 'required|integer|exists:courses,id',
            'part' => 'required|integer|exists:course_parts,id',
            'title' => 'required|string|max:255',
            'lesson' => 'nullable|file|mimes:mp4,avi,mkv,pdf|max:20480',
            'lesson_url' => 'nullable|string',
        ];



        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if (!$request->hasFile('lesson') && $request->input('lesson_url') == null) {
            return redirect()->back()->with('error', 'It is must to upload lesson');
        }

        DB::beginTransaction();

        try {
            $lessonPath = null;
            $type = null;

            if ($request->hasFile('lesson')) {
                $lessonPath = $request->file('lesson')->store('course_lessons', 'public');
                $fileType = $request->file('lesson')->getClientOriginalExtension();
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


    public function manageLessons($courseId)
    {
        try {
            $course = Course::findOrFail($courseId);
            $courseParts = CoursePart::where('course_id', $courseId)->get();
            $lessons = CourseMaterial::whereIn('part_id', $courseParts->pluck('id'))->with('coursePart')->get();
            // dd($lessons);
            return view('content.courses.manage-lessons', compact('course', 'lessons', 'courseParts'));
        } catch (\Exception $e) {
            Log::error('Error retrieving lessons: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue retrieving the lessons. Please try again.');
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
                    Storage::delete($courseMaterial->url);
                }
                $lessonPath = $request->file('lesson')->store('course_lessons', 'public');
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
        $course = Course::with(['courseParts.courseMaterials'])->find($id);

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

        $course->enrolled_students = $course->students()->count();
        $course->instructor = $course->creator->name;

        foreach ($course->courseParts as $part) {
            foreach ($part->courseMaterials as $material) {
                if ($material->type != "url") {
                    $material->url = asset('storage/' . $material->url);
                }
            }
        }

        // dd($course);

        return response()->json($course);
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
