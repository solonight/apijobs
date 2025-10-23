<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        if (auth()->user()->roles()->count() > 0) {
            return response()->json(['error' => 'Only users with no role can apply'], 403);
        }

        $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'cover_letter' => 'nullable|string',
        ]);

        $application = \App\Models\Application::create([
            'user_id' => auth()->id(),
            'job_id' => $request->job_id,
            'cover_letter' => $request->cover_letter,
        ]);

        return response()->json(['application' => $application], 201);
    }
}
