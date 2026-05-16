<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function getAllPermissions()
    {
        $permissions = Permission::all();

        return response()->json($permissions);
    }
}
