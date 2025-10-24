<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Jobs",
 *     description="API Endpoints for managing jobs"
 * )
 */
// The above annotation groups all job-related endpoints under the "Jobs" tag in Swagger UI.

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

        /**
         * @OA\Post(
         *     path="/api/jobs",
         *     tags={"Jobs"},
         *     summary="Create a new job",
         *     description="Creates a new job posting. Only employers with permission can create jobs.",
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             required={"title", "company", "location"},
         *             @OA\Property(property="title", type="string"),
         *             @OA\Property(property="company", type="string"),
         *             @OA\Property(property="location", type="string")
         *         )
         *     ),
         *     @OA\Response(
         *         response=201,
         *         description="Job created successfully",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="job", ref="#/components/schemas/Job")
         *         )
         *     ),
         *     @OA\Response(
         *         response=403,
         *         description="Unauthorized",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="error", type="string")
         *         )
         *     )
         * )
         */
        // The above annotation documents the POST /api/jobs endpoint.
    
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

        /**
         * @OA\Put(
         *     path="/api/jobs/{id}",
         *     tags={"Jobs"},
         *     summary="Update a job",
         *     description="Update a job posting. Only the employer who owns the job can update it.",
         *     @OA\Parameter(
         *         name="id",
         *         in="path",
         *         required=true,
         *         description="Job ID",
         *         @OA\Schema(type="integer")
         *     ),
         *     @OA\RequestBody(
         *         required=false,
         *         @OA\JsonContent(
         *             @OA\Property(property="title", type="string"),
         *             @OA\Property(property="company", type="string"),
         *             @OA\Property(property="location", type="string")
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Job updated successfully",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="job", ref="#/components/schemas/Job")
         *         )
         *     ),
         *     @OA\Response(
         *         response=403,
         *         description="Unauthorized",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="error", type="string")
         *         )
         *     )
         * )
         */
        // The above annotation documents the PUT /api/jobs/{id} endpoint.

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

        /**
         * @OA\Delete(
         *     path="/api/jobs/{id}",
         *     tags={"Jobs"},
         *     summary="Delete a job",
         *     description="Delete a job posting. Only the employer who owns the job can delete it.",
         *     @OA\Parameter(
         *         name="id",
         *         in="path",
         *         required=true,
         *         description="Job ID",
         *         @OA\Schema(type="integer")
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Job deleted successfully",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="message", type="string")
         *         )
         *     ),
         *     @OA\Response(
         *         response=403,
         *         description="Unauthorized",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="error", type="string")
         *         )
         *     )
         * )
         */
        // The above annotation documents the DELETE /api/jobs/{id} endpoint.
}
