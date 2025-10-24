<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;

/**
 * @OA\Tag(
 *     name="Applications",
 *     description="API Endpoints for managing job applications"
 * )
 */
// The above annotation groups all application-related endpoints under the "Applications" tag in Swagger UI.

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

    /**
     * @OA\Post(
     *     path="/api/applications",
     *     tags={"Applications"},
     *     summary="Create a new application",
     *     description="Creates a new job application. Only users can apply.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"job_id"},
     *             @OA\Property(property="job_id", type="integer"),
     *             @OA\Property(property="cover_letter", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Application created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="application", ref="#/components/schemas/Application")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Only users can apply",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    // The above annotation documents the POST /api/applications endpoint.

    /**
     * @OA\Get(
     *     path="/api/employer/applications",
     *     tags={"Applications"},
     *     summary="Get all job applications for jobs created by the employer",
     *     description="Returns all job applications for jobs created by the authenticated employer.",
     *     @OA\Response(
     *         response=200,
     *         description="List of job applications",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="applications",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Application")
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
    public function employerApplications(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasRole('employer')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get job IDs created by this employer
        $jobIds = $user->jobs()->pluck('id');
        // Get all applications for those jobs
        $applications = \App\Models\Application::whereIn('job_id', $jobIds)->get();

        return response()->json(['applications' => $applications]);
    }

    public function destroy($id){
        $application = Application::findOrFail($id);
        if(!auth()->user()->hasRole('user') || $application->user_id !=auth()->id()){
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $application->delete();
        return response()->json(['message' => 'Application deleted']);
    }

    /**
     * @OA\Delete(
     *     path="/api/applications/{id}",
     *     tags={"Applications"},
     *     summary="Delete an application",
     *     description="Delete a job application. Only the user who created the application can delete it.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Application ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Application deleted successfully",
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
    // The above annotation documents the DELETE /api/applications/{id} endpoint.
}
