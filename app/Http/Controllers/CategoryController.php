<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class CategoryController extends Controller
{

    public function downloadCsv(Request $request)
    {
        $query = Category::with(['subcategories.courses'])->withCount('courses');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('subcategories', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('subcategories.courses', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $categories = $query->get();

        $tempFile = tempnam(sys_get_temp_dir(), 'csv');
        $handle = fopen($tempFile, 'w');

        fputcsv($handle, ['Category No', 'Category Name', 'No. of Courses', 'Subcategories', 'Courses in Subcategories']);


        foreach ($categories as $category) {
            $subcategoryNames = $category->subcategories->pluck('name')->implode(', ');
            $subcategoryCoursesCounts = $category->subcategories->map(function ($subcategory) {
                return $subcategory->courses->count();
            })->implode(', ');

            fputcsv($handle, [
                $category->id,
                $category->name,
                $category->courses_count,
                $subcategoryNames,
                $subcategoryCoursesCounts,
            ]);
        }

        fclose($handle);

        $response = response()->download($tempFile, 'categories_and_subcategories.csv')->deleteFileAfterSend(true);

        return $response;
    }
    public function index()
    {
        return view('content.categories.create');
    }

    public function create(Request $request)
    {
        $messages = [
            'name.unique' => 'The category name has already been taken.',
            'subcategory.*.unique' => 'The subcategory ":input" has already been taken.',
            'subcategory.*.max' => 'The subcategory ":input" may not be greater than 255 characters.',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:30|unique:categories,name',
            'subcategory.*' => 'nullable|string|max:255|unique:subcategories,name',
        ], $messages);

        $subcategories = $request->input('subcategory', []);
        if (count($subcategories) !== count(array_unique($subcategories))) {
            return redirect()->back()->withErrors(['subcategory' => 'Duplicate subcategory names are not allowed.'])->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $category = Category::create([
            'name' => $request->name
        ]);

        foreach ($subcategories as $subcategoryName) {
            if (!empty($subcategoryName)) {
                Subcategory::create([
                    'category_id' => $category->id,
                    'name' => $subcategoryName,
                ]);
            }
        }

        if ($category) {
            return redirect()->back()->with('success', 'Category has been created successfully.');
        } else {
            return redirect()->back()->with('error', 'Something went wrong. Try again!');
        }
    }


    public function show(Request $request)
    {
        $search = $request->input('search');
        $categories = Category::withCount('courses')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->get();
        return view('content.categories.manage', compact('categories'));
    }

    public function getAllCategories()
    {
        $categories = Category::withCount('courses')->get();
        return response()->json($categories);
    }

    public function getCategoryCourses($id)
    {
        $category = Category::with('courses.courseParts.courseMaterials')->find($id);

        if (!$category) {
            return response()->json(['status' => 404, 'error' => 'Category not found'], 404);
        }

        $category->courses->transform(function ($course) {

            $course->thumbnail = $course->thumbnail ? asset('storage/' . $course->thumbnail) : null;
            $course->demo_video = $course->demo_video ? asset('storage/' . $course->demo_video) : null;

            $subcategory = Subcategory::find($course->sub_category);
            $category = Category::find($course->category);

            $course->course_category = $category;
            $course->sub_category = $subcategory;

            foreach ($course->courseParts as $part) {
                foreach ($part->courseMaterials as $material) {
                    $material->url = asset('storage/' . $material->url);
                }
            }

            return $course;
        });


        return response()->json($category->courses);
    }

    public function getCategoryQuizzes($id)
    {
        $category = Category::with('quizzes.questions')->find($id);

        if (!$category) {
            return response()->json(['status' => 404, 'error' => 'Category not found'], 404);
        }

        $category->quizzes->transform(function ($quiz) {
            $quiz->thumbnail = $quiz->thumbnail ? asset('storage/' . $quiz->thumbnail) : null;

            $subcategory = Subcategory::find($quiz->sub_category);
            $category = Category::find($quiz->category_id);

            $quiz->category = $category;
            $quiz->sub_category = $subcategory;

            return $quiz;
        });
        return response()->json($category->quizzes);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:30',
            'subcategories' => 'array',
            'subcategories.*.id' => 'nullable',
            'subcategories.*.name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors());
        }

        $category = Category::findOrFail($id);
        $category->name = $request->input('name');
        $category->save();

        $subcategories = $request->input('subcategories', []);
        $subcategoryNames = [];

        foreach ($subcategories as $subcategoryData) {
            $subcategory = Subcategory::find($subcategoryData['id']);
            if (isset($subcategory)) {
                $subcategory->name = $subcategoryData['name'];
                $subcategory->save();
                $subcategoryNames[] = $subcategory->name;
            } else {
                $existingSubcategory = Subcategory::where('category_id', $category->id)
                    ->where('name', $subcategoryData['name'])
                    ->first();

                if ($existingSubcategory) {
                    return redirect()->back()->with('error', "Subcategory name '{$subcategoryData['name']}' is already used in this category.");
                } else {
                    $newSubcategory = Subcategory::create([
                        'category_id' => $category->id,
                        'name' => $subcategoryData['name'],
                    ]);
                    $subcategoryNames[] = $newSubcategory->name;
                }
            }
        }

        $existingSubcategoryNames = Subcategory::where('category_id', $category->id)
            ->pluck('name')
            ->toArray();

        $subcategoriesToDelete = array_diff($existingSubcategoryNames, $subcategoryNames);

        if (!empty($subcategoriesToDelete)) {
            Subcategory::where('category_id', $category->id)
                ->whereIn('name', $subcategoriesToDelete)
                ->delete();
        }

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->subcategories()->delete();
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}