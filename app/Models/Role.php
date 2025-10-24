<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

/**
 * @OA\Schema(
 *     schema="Role",
 *     type="object",
 *     title="Role",
 *     description="Role model",
 *     required={"id", "name"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="admin"),
 *     @OA\Property(property="guard_name", type="string", example="web"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
// The above annotation describes the Role model for Swagger documentation.

class Role extends SpatieRole
{
    public $guarded = [];
}
