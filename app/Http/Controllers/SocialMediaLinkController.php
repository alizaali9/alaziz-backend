<?php

namespace App\Http\Controllers;

use App\Models\SocialMediaLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SocialMediaLinkController extends Controller
{
    /**
     * Display a listing of the social media links.
     */
    public function manage()
    {
        $links = SocialMediaLink::all();
        return view('content.social-links.manage', compact('links'));
    }

    /**
     * Show the form for creating a new social media link.
     */
    public function create()
    {
        return view('content.social-links.create');
    }

    /**
     * Store a newly created social media link in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'social_account' => 'required|string|max:255',
            'social_link' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        SocialMediaLink::create($request->only(['social_account', 'social_link']));

        return redirect()->route('social.links.create')->with('success', 'Social media link added successfully.');
    }



    /**
     * Update the specified social media link in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'social_account' => 'required|string|max:255',
            'social_link' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $link = SocialMediaLink::findOrFail($id);
        $link->update($request->only(['social_account', 'social_link']));

        return redirect()->route('social.links.manage')->with('success', 'Social media link updated successfully.');
    }

    /**
     * Remove the specified social media link from storage.
     */
    public function delete($id)
    {
        $link = SocialMediaLink::findOrFail($id);
        $link->delete();

        return redirect()->route('social.links.manage')->with('success', 'Social media link deleted successfully.');
    }
}
