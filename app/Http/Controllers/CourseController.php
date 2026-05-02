<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index()
    {
        return auth()->user()->courses()->orderBy('day')->orderBy('start_time')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'time' => 'required|string',
            'location' => 'required|string',
            'day' => 'required|string',
            'color' => 'nullable|string',
        ]);

        // Parse time into start_time and end_time
        $parts = explode('-', $request->time);
        
        $course = auth()->user()->courses()->create([
            'id' => Str::uuid(),
            'title' => $request->name,
            'location' => $request->location,
            'day' => $request->day,
            'start_time' => trim($parts[0] ?? ''),
            'end_time' => trim($parts[1] ?? ''),
            'color' => $request->color,
        ]);
        
        return response()->json($course, 201);
    }

    public function update(Request $request, Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $parts = explode('-', $request->time);
        
        $course->update([
            'title' => $request->name,
            'location' => $request->location,
            'day' => $request->day,
            'start_time' => trim($parts[0] ?? ''),
            'end_time' => trim($parts[1] ?? ''),
            'color' => $request->color,
        ]);
        
        return response()->json($course);
    }

    public function destroy(Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $course->delete();
        return response()->json(['message' => 'Deleted']);
    }
}