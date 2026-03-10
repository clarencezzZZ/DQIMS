<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Check if this is a profile picture upload only
        if ($request->hasFile('profile_picture')) {
            // Profile picture upload - validate only the image
            $validator = validator($request->all(), [
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,jfif,webp,bmp,svg|max:2048',
            ]);
        } else {
            // Full profile update - validate all fields
            $validator = validator($request->all(), [
                'name' => 'required|string|max:255',
                'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
                'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => 'nullable|min:6|confirmed',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,jfif,webp,bmp,svg|max:2048',
            ]);
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture && file_exists(public_path('uploads/profiles/' . $user->profile_picture))) {
                unlink(public_path('uploads/profiles/' . $user->profile_picture));
            }
            
            // Store new profile picture
            $file = $request->file('profile_picture');
            $filename = time() . '_' . $user->username . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/profiles'), $filename);
            $user->profile_picture = $filename;
        }
        
        // Update other fields only if provided
        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        
        if ($request->filled('username')) {
            $user->username = $request->username;
        }
        
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        
        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }
}
