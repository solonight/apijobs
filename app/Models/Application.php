<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Application",
 *     type="object",
 *     title="Application",
 *     description="Application model",
 *     required={"id", "user_id", "job_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=2),
 *     @OA\Property(property="job_id", type="integer", example=3),
 *     @OA\Property(property="cover_letter", type="string", example="I am interested in this job."),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
// The above annotation describes the Application model for Swagger documentation.

class Application extends Model
{
    use HasFactory;
    
        protected $fillable = [
        'user_id',
        'job_id',
        'cover_letter'
    ];
}
