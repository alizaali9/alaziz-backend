<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    public function getSliders()
    {
        $sliders = Slider::all();

        $sliders = $sliders->map(function ($slider) {
            return [
                'image' => $slider->image ? asset('storage/' . $slider->image) : null,
                'link' => $slider->link,
            ];
        });

        return response()->json([
            'status' => 200,
            'sliders' => $sliders
        ], 200);
    }



    public function index()
    {
        return view("content.sliders.create");
    }
    public function manage()
    {
        $sliders = Slider::all();
        return view("content.sliders.manage", compact("sliders"));
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slider' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'link' => 'nullable|url'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $slider = new Slider();

        if ($request->hasFile('slider')) {
            $slider->image = $request->file('slider')->store('sliders', 'public');
        }

        $slider->link = $request->input('link');
        $slider->save();

        return redirect()->back()->with('success', 'Slider image uploaded successfully!');
    }

    public function destroy($id)
    {
        $slider = Slider::findOrFail($id);

        if ($slider->image) {
            Storage::disk('public')->delete($slider->image);
        }

        $slider->delete();

        return redirect()->back()->with('success', 'Slider deleted successfully!');
    }

}
