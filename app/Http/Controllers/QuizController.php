<?php

namespace App\Http\Controllers;

use App\Imports\QuizImport;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizRating;
use App\Models\Student;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class QuizController extends Controller
{
    public function getAllQuizes()
    {
        $token = request()->header('Authorization');

        $quizzes = Quiz::with(['questions', 'category', 'subcategory'])
            ->where('status', true)
            ->get();

        $student = null;
        if ($token) {
            $student = Student::where('api_token', $token)->first();
        }

        $quizzes->transform(function ($quiz) use ($student) {
            $enrollmentCount = $quiz->enrollments()->count();
            $thumbnail = $quiz->thumbnail ? asset('storage/' . $quiz->thumbnail) : null;
            $quiz->thumbnail = $thumbnail;
            $quiz->enrolled_students = $enrollmentCount;

            if ($student) {
                $quizRating = QuizRating::where('user_id', $student->id)
                    ->where('quiz_id', $quiz->id)
                    ->first();

                if ($quizRating) {
                    $stars = $quizRating->stars;
                    $quiz->isRated = true;
                    $quiz->stars = $stars;
                } else {
                    $quiz->isRated = false;
                }
            } else {
                $quiz->isRated = false;
            }

            return $quiz;
        });

        if ($quizzes->isEmpty()) {
            return response()->json(['error' => 'Quizzes not found'], 404);
        }

        return response()->json($quizzes);
    }



    public function manageQuizzes()
    {
        $query = Quiz::with(['questions', 'category', 'subcategory']);

        if (request()->has('search')) {
            $search = request()->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('subcategory', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('timelimit', 'LIKE', "%{$search}%")
                    ->orWhere('price', 'LIKE', "%{$search}%")
                    ->orWhere('tries', 'LIKE', "%{$search}%");
            });
        }

        $quizzes = $query->get();
        $subcategories = Subcategory::all();

        return view('content.quizzes.manage', compact('quizzes', 'subcategories'));
    }

    public function downloadQuizzesCSV(Request $request)
    {
        try {
            $query = Quiz::with('subcategory');

            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhereHas('subcategory', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        })
                        ->orWhere('timelimit', 'LIKE', "%{$search}%")
                        ->orWhere('price', 'LIKE', "%{$search}%")
                        ->orWhere('tries', 'LIKE', "%{$search}%");
                });
            }

            $quizzes = $query->get();

            $csvData = [];

            $csvData[] = ['Quiz Name', 'Quiz Category', 'Quiz Duration', 'Quiz Price', 'Quiz Discount', 'No. of Tries'];

            foreach ($quizzes as $quiz) {
                $csvData[] = [
                    $quiz->name,
                    $quiz->subcategory->name,
                    $quiz->timelimit,
                    $quiz->price,
                    $quiz->discount,
                    $quiz->tries,
                ];
            }

            $filename = "quizzes.csv";
            $handle = fopen($filename, 'w+');
            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);

            return response()->download($filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Error downloading quizzes CSV: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue downloading the CSV. Please try again.');
        }
    }



    public function getQuizById($id)
    {
        $quiz = Quiz::with(['questions', 'category', 'subcategory'])->find($id);
        $enrollmentCount = $quiz->enrollments()->count();
        $quiz->enrolled_students = $enrollmentCount;
        $token = request()->header('Authorization');
        $student = null;
        if ($token) {
            $student = Student::where('api_token', $token)->first();
        }


        if (!$quiz) {
            return response()->json(['error' => 'Quiz not found'], 404);
        }

        if ($student) {
            $quizRating = QuizRating::where('user_id', $student->id)
                ->where('quiz_id', $quiz->id)
                ->first();

            if ($quizRating) {
                $stars = $quizRating->stars;
                $quiz->isRated = true;
                $quiz->stars = $stars;
            } else {
                $quiz->isRated = false;
            }
        } else {
            $quiz->isRated = false;
        }

        return response()->json($quiz);
    }

    public function create()
    {
        $categories = Subcategory::all();
        return view('content.quizzes.create', compact('categories'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:18|unique:quizzes,name',
            'status' => 'required|boolean',
            'thumbnail' => 'nullable|file|mimes:png,jpg,jpeg',
            'price' => 'required|integer',
            'discount' => 'nullable|integer',
            'tries' => 'nullable|integer',
            'timelimit' => 'required|integer',
            'sub_category' => 'required|integer|exists:subcategories,id',
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $sheets = Excel::toCollection(null, $request->file('file'));
        if ($sheets->count() > 1) {
            return redirect()->back()->withErrors(['file' => 'The file contains more than one sheet.']);
        }

        $subcategory = Subcategory::find($request->sub_category);

        // dd($subcategory);
        $category = $subcategory->category_id;

        // dd($category);

        $quizData = $request->only(['name', 'status', 'tries', 'price', 'discount', 'timelimit', 'sub_category']);
        $quizData["category_id"] = $category;
        $quizData["discount"] = $request->discount ? $request->discount : 0;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('quiz_thumbnails', 'public');
            //  dd($thumbnailPath);
            $quizData["thumbnail"] = $thumbnailPath;
        }


        // dd($quizData);

        Excel::import(new QuizImport($quizData), $request->file('file'));

        return redirect()->back()->with('success', 'Quiz imported successfully.');
    }

    public function destroy($id)
    {
        $quiz = Quiz::with('questions')->find($id);

        if (!$quiz) {
            return redirect()->back()->withErrors(['error' => 'Quiz not found']);
        }

        try {
            if ($quiz->thumbnail) {
                Storage::disk('public')->delete($quiz->thumbnail);
            }
            $quiz->questions()->delete();

            $quiz->delete();

            return redirect()->route('manage.quiz')->with('success', 'Quiz deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'There was an issue deleting the quiz. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:18|unique:quizzes,name,' . $id,
            'timelimit' => 'required|integer',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'sub_category' => 'required|integer|exists:subcategories,id',
            'tries' => 'nullable|integer',
            'thumbnail' => 'nullable|file|mimes:png,jpg,jpeg',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $quiz = Quiz::findOrFail($id);

        $quizData = [
            'name' => $request->name,
            'timelimit' => $request->timelimit,
            'price' => $request->price,
            'discount' => $request->discount ? $request->discount : 0,
            'sub_category' => $request->sub_category,
            'tries' => $request->tries,
            'thumbnail' => $request->hasFile('thumbnail') ? $request->file('thumbnail')->store('quiz_thumbnails', 'public') : $quiz->thumbnail,
        ];

        $quiz->update($quizData);

        return redirect()->back()->with('success', 'Quiz updated successfully!');
    }


    public function updateStatus(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->status = $request->status;
        $quiz->save();

        return response()->json(['success' => true]);
    }


    public function updateRatings(Request $request, $quizId)
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
            $quizRating = QuizRating::updateOrCreate(
                [
                    'user_id' => $student->id,
                    'quiz_id' => $quizId,
                ],
                [
                    'stars' => $request->stars,
                ]
            );

            $quiz = Quiz::find($quizId);

            $noOfRaters = QuizRating::where('quiz_id', $quizId)->count();

            $quiz->no_of_raters = $noOfRaters;

            $totalStars = QuizRating::where('quiz_id', $quizId)->sum('stars');
            $quiz->quiz_stars = $noOfRaters > 0
                ? number_format($totalStars / $noOfRaters, 2, '.', '')
                : 0;


            $quiz->save();

            return response()->json([
                'message' => $quizRating->wasRecentlyCreated
                    ? 'Quiz rating created successfully.'
                    : 'Quiz rating updated successfully.',
                'quiz_stars' => $quiz->quiz_stars,
                'no_of_raters' => $quiz->no_of_raters,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'error' => 'There was an issue updating the course ratings. Please try again.'], 500);
        }
    }
}
