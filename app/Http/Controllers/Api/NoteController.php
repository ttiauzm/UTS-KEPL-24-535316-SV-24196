<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /** GET /api/notes */
    public function index(): JsonResponse
    {
        return response()->json(Note::latest()->get());
    }

    /** POST /api/notes */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        $note = Note::create($validated);

        return response()->json($note, 201);
    }

    /** GET /api/notes/{note} */
    public function show(Note $note): JsonResponse
    {
        return response()->json($note);
    }

    /** PUT/PATCH /api/notes/{note} */
    public function update(Request $request, Note $note): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        $note->update($validated);

        return response()->json($note);
    }

    /** DELETE /api/notes/{note} */
    public function destroy(Note $note): JsonResponse
    {
        $note->delete();

        return response()->json(['message' => 'Catatan berhasil dihapus.']);
    }
}
