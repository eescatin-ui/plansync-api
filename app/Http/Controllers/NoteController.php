<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NoteController extends Controller
{
    public function index()
    {
        return auth()->user()->notes()->orderBy('updated_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        $note = auth()->user()->notes()->create([
            'id' => Str::uuid(),
            'title' => $request->title,
            'content' => $request->content,
            'tags' => $request->tags,
        ]);
        
        return response()->json($note, 201);
    }

    public function update(Request $request, Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $note->update([
            'title' => $request->title,
            'content' => $request->content,
            'tags' => $request->tags,
        ]);
        
        return response()->json($note);
    }

    public function destroy(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $note->delete();
        return response()->json(['message' => 'Deleted']);
    }
}