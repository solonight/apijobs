<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('user')) {
            return response()->json(['error' => 'Only users can apply'], 403);
        }

        $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'cover_letter' => 'nullable|string',
        ]);

        $application = Application::create([
            'user_id' => auth()->id(),
            'job_id' => $request->job_id,
            'cover_letter' => $request->cover_letter,
        ]);

        return response()->json(['application' => $application], 201);
    }
    public function destroy($id){
        $application = Application::findOrFail($id);
        if(!auth()->user()->hasRole('user') || $application->user_id !=auth()->id()){
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $application->delete();
        return response()->json(['message' => 'Application deleted']);
    }
}
