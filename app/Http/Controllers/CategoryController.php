<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
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
            'name' => 'required|string|max:255|unique:categories,name',
            'subcategory.*' => 'nullable|string|max:255|unique:subcategories,name',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create the new category
        $category = Category::create([
            'name' => $request->name
        ]);

        // Create associated subcategories if provided
        $subcategories = $request->input('subcategory', []);
        foreach ($subcategories as $subcategoryName) {
            if (!empty($subcategoryName)) {
                Subcategory::create([
                    'category_id' => $category->id,
                    'name' => $subcategoryName,
                ]);
            }
        }

        if ($category && $subcategories) {
            return redirect()->back()->with('success', 'Category has been created successfully.');
        } else {
            return redirect()->back()->with('error', 'Something went wrong. Try again!');
        }
    }

    public function show()
    {
        $categories = Category::withCount('courses')->with(['subcategories', 'subcategories.courses'])->get();

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

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
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
