<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SubAdminController extends Controller
{
    public function index()
    {
        return view('content.subadmins.create');
    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 3,
            'remember_token' => Str::random(10),
        ]);

        if ($user) {
            return back()->with('success', 'Sub Admin has been created successfully.');
        }

        return back()->with('error', 'An error occurred while creating the instructor.');
    }

    public function manage(Request $request)
    {
        $query = User::where('role', 3);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $subadmins = $query->get();
        return view('content.subadmins.manage', compact('subadmins'));
    }

    public function downloadCSV(Request $request)
    {
        $query = User::where('role', 3);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $subadmins = $query->get();

        $csvFileName = 'subadmins_' . now()->format('Y_m_d_H_i_s') . '.csv';
        $handle = fopen($csvFileName, 'w');

        $csvData = [
            ['Name', 'Email']
        ];

        foreach ($subadmins as $subadmin) {
            $csvData[] = [
                $subadmin->name,
                $subadmin->email
            ];
        }

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return response()->download($csvFileName)->deleteFileAfterSend(true);
    }


    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $subadmin = User::find($request->id);

        $subadmin->name = $request->name;
        $subadmin->email = $request->email;

        $subadmin->save();

        return redirect()->back()->with('success', 'Sub Admin updated successfully');
    }

    public function destroy($id)
    {
        $subadmin = User::findOrFail($id);

        $subadmin->delete();

        return back()->with('success', 'Sub Admin has been deleted successfully.');
    }



}
