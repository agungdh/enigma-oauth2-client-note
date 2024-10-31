<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Note::where('user_id', session('user.id'))->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'note' => 'required',
        ]);

        $note = new Note();

        $note->user_id = session('user.id');
        $note->title = $request->title;
        $note->note = $request->note;
        $note->date_time = now();

        $note->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        return $note;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        $request->validate([
            'title' => 'required',
            'note' => 'required',
        ]);

        $note->user_id = session('user.id');
        $note->title = $request->title;
        $note->note = $request->note;
        $note->date_time = now();

        $note->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        $note->delete();
    }
}
