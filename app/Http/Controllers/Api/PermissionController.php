<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function getAllPermissions()
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        $permissions = Permission::all();

        return response()->json($permissions);
    }
}
