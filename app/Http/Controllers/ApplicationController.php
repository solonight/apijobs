<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\application;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        // Only allow users with no role
        if (auth()->user()->roles()->count() > 0) {
            return response()->json(['error' => 'Only users with no role can apply'], 403);
        }

        $request->validate([
            'title' => 'required|string|exists:jobs,title',
            'cover_letter' => 'nullable|string',
        ]);

        $job = \App\Models\Job::where('title', $request->title)->first();

        if (!$job) {
            return response()->json(['error' => 'Job not found'], 404);
        }

        $application = \App\Models\Application::create([
            'user_id' => auth()->id(),
            'job_id' => $job->id,
            'cover_letter' => $request->cover_letter,
        ]);

        return response()->json(['application' => $application], 201);
    }
}
