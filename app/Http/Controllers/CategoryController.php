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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subcategory.*' => 'nullable|string|max:255|',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $category = Category::create([
            'name' => $request->name
        ]);

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
            return redirect()->back()->with('success', 'Category has been created Successfully.');
        } else {
            return redirect()->back()->with('error', 'Something went wrong. Try Again!');
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
