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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $job = \App\Models\Job::create($validated);

        return response()->json(['job' => $job], 201);
    }
}
