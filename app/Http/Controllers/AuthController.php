<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ========== REGISTER ==========
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'avatar_color' => $this->randomColor(),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // ========== LOGIN ==========
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
        ]);
    }

    // ========== LOGOUT ==========
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    // ========== GET CURRENT USER ==========
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    // ========== UPDATE PROFILE ==========
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
            'avatar_color' => 'nullable|string',
        ]);

        $request->user()->update($request->only(['name', 'email', 'avatar_color']));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $request->user()->fresh(),
        ]);
    }

    // ========== UPLOAD PROFILE IMAGE ==========
    public function uploadProfileImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->user()->profile_image) {
            Storage::disk('public')->delete($request->user()->profile_image);
        }

        $path = $request->file('image')->store('profile-images', 'public');
        $request->user()->update(['profile_image' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Profile image uploaded',
            'url' => asset('storage/' . $path),
        ]);
    }

    // ========== GET PROFILE IMAGE ==========
    public function getProfileImage(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'url' => $user->profile_image ? asset('storage/' . $user->profile_image) : null,
        ]);
    }

    // ========== REMOVE PROFILE IMAGE ==========
    public function removeProfileImage(Request $request)
    {
        if ($request->user()->profile_image) {
            Storage::disk('public')->delete($request->user()->profile_image);
            $request->user()->update(['profile_image' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile image removed',
        ]);
    }

    // ========== CHANGE PASSWORD ==========
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update(['password' => $request->new_password]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }

    // ========== DELETE ACCOUNT ==========
    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->courses()->delete();
        $user->tasks()->delete();
        $user->notes()->delete();
        $user->reminders()->delete();
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully',
        ]);
    }

    // ========== SAVE PREFERENCES ==========
    public function savePreferences(Request $request)
    {
        $request->validate([
            'theme' => 'nullable|in:light,dark',
            'email_notifications' => 'nullable|boolean',
            'task_reminders' => 'nullable|boolean',
            'class_notifications' => 'nullable|boolean',
        ]);

        $request->user()->update([
            'preferences' => $request->only([
                'theme', 'email_notifications', 'task_reminders', 'class_notifications'
            ])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Preferences saved',
        ]);
    }

    // ========== GET PREFERENCES ==========
    public function getPreferences(Request $request)
    {
        return response()->json([
            'preferences' => $request->user()->preferences ?? [
                'theme' => 'light',
                'email_notifications' => true,
                'task_reminders' => true,
                'class_notifications' => true,
            ],
        ]);
    }

    // ========== HELPER ==========
    private function randomColor()
    {
        $colors = ['#4361EE', '#3A0CA3', '#7209B7', '#F72585', '#4CC9F0', '#4895EF', '#560BAD', '#E63946'];
        return $colors[array_rand($colors)];
    }
}