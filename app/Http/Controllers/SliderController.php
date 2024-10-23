<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class SliderController extends Controller
{
    public function getSliders()
    {
        $sliders = Slider::all();

        $sliders = $sliders->map(function ($slider) {
            $fileURL = Storage::disk('google')->url($slider->image);
            return [
                'image' => $slider->image ? $fileURL : null,
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
        foreach ($sliders as $slider) {
            $fileUrl = Storage::disk('google')->url($slider->image) . "&authuser=0";
            $slider->image = $fileUrl;
        }
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
            $sliderFile = $request->file('slider');

            $extension = $sliderFile->getClientOriginalExtension();
            $fileData = File::get($sliderFile);
            $uniqueFileName = env('GOOGLE_DERIVE_FOLDER_NAME') . '/sliders/slider_' . uniqid() . '.' . $extension;
            Storage::disk('google')->put($uniqueFileName, $fileData);
            Storage::disk('google')->setVisibility($uniqueFileName, 'public');
            $slider->image = $uniqueFileName;
        }

        $slider->link = $request->input('link');
        $slider->save();

        return redirect()->back()->with('success', 'Slider image uploaded successfully!');
    }

    public function destroy($id)
    {
        $slider = Slider::findOrFail($id);

        if ($slider->image) {
            Storage::disk('google')->delete($slider->image);
        }

        $slider->delete();

        return redirect()->back()->with('success', 'Slider deleted successfully!');
    }

}
