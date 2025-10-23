<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JobController extends Controller
{
    public function store(Request $request)
    {
        if (!auth()->user()->can('create jobs')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate and create the job
        $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $job = \App\Models\Job::create([
            'title' => $request->title,
            'company' => $request->company,
            'location' => $request->location,
            'user_id' => auth()->id(), // track employer
        ]);

        return response()->json(['job' => $job], 201);
    }
    
    public function update(Request $request, Job $job)
    {
        $user = auth()->user();

        // Only allow if user is employer, owns the job, and has permission
        if (!$user->hasRole('employer') || $job->user_id !== $user->id || !$user->can('update jobs')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'company' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
        ]);

        $job->update($request->only(['title', 'company', 'location']));

        return response()->json(['job' => $job]);
    }

    public function destroy(Job $job)
    {
        $user = auth()->user();

        // Only allow if user is employer, owns the job, and has permission
        if (!$user->hasRole('employer') || $job->user_id !== $user->id || !$user->can('delete jobs')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $job->delete();

        return response()->json(['message' => 'Job deleted']);
    }
}
