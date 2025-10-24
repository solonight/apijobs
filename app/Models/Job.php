<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Job",
 *     type="object",
 *     title="Job",
 *     description="Job model",
 *     required={"id", "title", "company", "location", "user_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Software Engineer"),
 *     @OA\Property(property="company", type="string", example="Tech Corp"),
 *     @OA\Property(property="location", type="string", example="New York"),
 *     @OA\Property(property="user_id", type="integer", example=2),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
// The above annotation describes the Job model for Swagger documentation.



class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'company',
        'location',
        'user_id',
    ];
}
