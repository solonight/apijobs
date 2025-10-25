<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;

/**
 * @OA\Tag(
 *     name="Jobs",
 *     description="API Endpoints for managing jobs"
 * )
 */
class JobController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user/jobs",
     *     tags={"Jobs"},
     *     summary="User views all jobs",
     *     description="Allows a user to view all available jobs.",
     *     @OA\Response(
     *         response=200,
     *         description="List of jobs",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="jobs",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Job")
     *             )
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
    public function userViewJobs(Request $request)
    /**
     * @OA\Get(
     *     path="/api/user/jobs",
     *     tags={"Jobs"},
     *     summary="Get all jobs for users",
     *     description="Returns a list of all jobs. Only accessible by users with the 'user' role.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="jobs",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Job")
     *             )
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
    {
        $user = auth()->user();
        if (!$user->hasRole('user')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $jobs = Job::all();
        return response()->json(['jobs' => $jobs]);
    }

    // ...existing code...
            /**
             * @OA\Put(
             *     path="/api/jobs/{id}",
             *     tags={"Jobs"},
             *     summary="Update a job",
             *     description="Update a job posting. Only the employer who owns the job can update it.",
             *     security={{"bearerAuth":{}}},
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

    // ...existing code...
    /**
     * @OA\Delete(
     *     path="/api/employer/jobs/{id}",
     *     tags={"Jobs"},
     *     summary="Employer deletes their own job",
     *     description="Allows an employer to delete a job they created.",
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
    public function employerDeleteJob($id)
    {
        $user = auth()->user();
        $job = \App\Models\Job::findOrFail($id);
        if (!$user->hasRole('employer') || $job->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $job->delete();
        return response()->json(['message' => 'Job deleted']);
    }
}
