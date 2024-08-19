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
        return view('content.courses.manage', compact('courses'));
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
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'level' => 'required|string',
            'language' => 'required|string',
            'price' => 'required|numeric',
            'sub_category' => 'required|integer|exists:subcategories,id',
            'demo' => 'nullable|file|mimes:mp4,avi,mkv|max:20480',
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
            }

            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
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

        $request->validate([
            'course_id' => 'required|integer|exists:courses,id',
            'name.*' => 'required|string|max:255',
        ]);

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
            // dd('Error storing course parts: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while creating course parts. Please try again.');
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


    public function getCourseDetails($id)
    {
        $course = Course::with(['courseParts.courseMaterials'])->find($id);

        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        $course->thumbnail = $course->thumbnail ? asset('storage/' . $course->thumbnail) : null;
        $course->demo_video = $course->demo_video ? asset('storage/' . $course->demo_video) : null;

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
