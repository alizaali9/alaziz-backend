<?php

namespace App\Http\Controllers;

use App\Imports\QuizImport;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Student;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class QuizController extends Controller
{
    public function getAllQuizes()
    {
        $quizzes = Quiz::with(['questions', 'category', 'subcategory'])->get();

        $quizzes->transform(function ($quiz) {
            $thumbnail = $quiz->thumbnail ? Storage::disk('google')->url($quiz->thumbnail) : null;
            $quiz->thumbnail = $thumbnail;

            return $quiz;
        });

        if (!$quizzes) {
            return response()->json(['error' => 'Quizes not found'], 404);
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
        foreach ($quizzes as $quiz) {
            $quiz->thumbnail = Storage::disk('google')->url($quiz->thumbnail);
        }

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

            $csvData[] = ['Quiz Name', 'Quiz Category', 'Quiz Duration', 'Quiz Price', 'No. of Tries'];

            foreach ($quizzes as $quiz) {
                $csvData[] = [
                    $quiz->name,
                    $quiz->subcategory->name,
                    $quiz->timelimit,
                    $quiz->price,
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

        if (!$quiz) {
            return response()->json(['error' => 'Quiz not found'], 404);
        }

        $quiz->thumbnail = Storage::disk('google')->url($quiz->thumbnail);

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

        $quizData = $request->only(['name', 'status', 'tries', 'price', 'timelimit', 'sub_category']);
        $quizData["category_id"] = $category;
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');

            $extension = $thumbnail->getClientOriginalExtension();
            $fileData = File::get($thumbnail);
            $thumbnailPath = env('GOOGLE_DERIVE_FOLDER_NAME') . '/quizzes/quiz_' . uniqid() . '.' . $extension;
            Storage::disk('google')->put($thumbnailPath, $fileData);
            Storage::disk('google')->setVisibility($thumbnailPath, 'public');

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
                Storage::disk('google')->delete($quiz->thumbnail);
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

        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:18|unique:quizzes,name,' . $id,
            'timelimit' => 'required|integer',
            'price' => 'required|numeric|between:0,99999.99',
            'sub_category' => 'required|integer|exists:subcategories,id',
            'tries' => 'nullable|integer',
            'thumbnail' => 'nullable|file|mimes:png,jpg,jpeg',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $quiz = Quiz::findOrFail($id);

        $thumbnail = $request->file('thumbnail');

        $extension = $thumbnail->getClientOriginalExtension();
        $fileData = File::get($thumbnail);
        $thumbnailPath = env('GOOGLE_DERIVE_FOLDER_NAME') . '/quizzes/quiz_' . uniqid() . '.' . $extension;
        Storage::disk('google')->put($thumbnailPath, $fileData);
        Storage::disk('google')->setVisibility($thumbnailPath, 'public');


        $quizData = [
            'name' => $request->name,
            'timelimit' => $request->timelimit,
            'price' => $request->price,
            'sub_category' => $request->sub_category,
            'tries' => $request->tries,
            'thumbnail' => $request->hasFile('thumbnail') ? $thumbnailPath : $quiz->thumbnail,
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


}
