<?php

namespace App\Http\Controllers;

use App\Imports\QuizImport;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class QuizController extends Controller
{
    public function getAllQuizes()
    {
        $quizzes = Quiz::with(['questions', 'category', 'subcategory'])->get();

        if (!$quizzes) {
            return response()->json(['error' => 'Quizes not found'], 404);
        }

        return response()->json($quizzes);
    }

    public function getQuizById($id)
    {
        $quiz = Quiz::with(['questions', 'category', 'subcategory'])->find($id);

        if (!$quiz) {
            return response()->json(['error' => 'Quiz not found'], 404);
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
            'name' => 'required|string',
            'status' => 'required|boolean',
            'thumbnail' => 'nullable|mimes:png,jpg,jpeg',
            'price' => 'required|integer',
            'tries' => 'nullable|integer',
            'timelimit' => 'required|integer',
            'sub_category' => 'required|integer|exists:subcategories,id',
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $subcategory = Subcategory::find($request->sub_category);

        // dd($subcategory);
        $category = $subcategory->category_id;

        // dd($category);

        $quizData = $request->only(['name', 'status', 'thumbnail', 'tries', 'price', 'timelimit', 'sub_category']);
        $quizData["category_id"] = $category;

        // dd($quizData);

        Excel::import(new QuizImport($quizData), $request->file('file'));

        return redirect()->back()->with('success', 'Quiz imported successfully.');
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'status' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'thumbnail' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'timelimit' => 'required|integer',
            'tries' => 'required|integer',
            'category_id' => 'required|integer|exists:categories,id',
            'subcategory_id' => 'required|integer|exists:subcategories,id',
            'questions.*.question' => 'required|string',
            'questions.*.option_a' => 'required|string',
            'questions.*.option_b' => 'required|string',
            'questions.*.option_c' => 'required|string',
            'questions.*.option_d' => 'required|string',
            'questions.*.answer' => 'required|string|in:a,b,c,d',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        try {
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('quiz_thumbnails', 'public');
            }

            $quiz = new Quiz();
            $quiz->status = $request->status;
            $quiz->name = $request->name;
            $quiz->thumbnail = $thumbnailPath ?? null;
            $quiz->time_limit = $request->timelimit;
            $quiz->category_id = $request->category_id;
            $quiz->subcategory_id = $request->subcategory_id;
            $quiz->save();

            foreach ($request->questions as $questionData) {
                $question = new Question();
                $question->quiz_id = $quiz->id;
                $question->question = $questionData['question'];
                $question->option_a = $questionData['option_a'];
                $question->option_b = $questionData['option_b'];
                $question->option_c = $questionData['option_c'];
                $question->option_d = $questionData['option_d'];
                $question->answer = $questionData['answer'];
                $question->save();
            }

            return redirect()->route('quizzes.index')->with('success', 'Quiz created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'There was an issue creating the quiz. Please try again.']);
        }
    }

}
