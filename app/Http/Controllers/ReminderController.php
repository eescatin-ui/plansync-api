<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReminderController extends Controller
{
    public function index()
    {
        return auth()->user()->reminders()->orderBy('created_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'time' => 'nullable|string',
            'type' => 'required|in:class,task,personal',
            'color' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $reminder = auth()->user()->reminders()->create([
            'id' => Str::uuid(),
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'note' => $request->subtitle,
            'type' => $request->type ?? 'personal',
            'color' => $request->color ?? '#4361ee',
            'icon' => $request->icon ?? 'bell',
            'remind_at' => now(),
        ]);
        
        return response()->json($reminder, 201);
    }

    public function update(Request $request, Reminder $reminder)
    {
        if ($reminder->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reminder->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'note' => $request->subtitle,
            'type' => $request->type ?? 'personal',
            'color' => $request->color ?? '#4361ee',
            'icon' => $request->icon ?? 'bell',
        ]);
        
        return response()->json($reminder);
    }

    public function destroy(Reminder $reminder)
    {
        if ($reminder->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $reminder->delete();
        return response()->json(['message' => 'Deleted']);
    }
}