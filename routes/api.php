<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AdminTranslationController;
use App\Http\Controllers\Api\Admin\MediaController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\EmailTemplateController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\LocalizationController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\TestimonialController;
use Illuminate\Support\Facades\Route;

Route::middleware('json.response')->group(function () {
    Route::get('/localization', [LocalizationController::class, 'index']);
    Route::get('/translations/{locale}', [LocalizationController::class, 'translations']);

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/profile/avatar', [AuthController::class, 'uploadAvatar']);
        Route::delete('/profile/avatar', [AuthController::class, 'deleteAvatar']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);

        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        Route::post('/users/invite', [UserController::class, 'invite']);
        Route::post('/users/{user}/assign-role', [UserController::class, 'assignRole']);
        Route::post('/users/{user}/give-permission', [UserController::class, 'givePermission']);
        Route::post('/users/{user}/revoke-permission', [UserController::class, 'revokePermission']);
        Route::post('/users/{user}/avatar', [UserController::class, 'uploadAvatar']);
        Route::delete('/users/{user}/avatar', [UserController::class, 'deleteAvatar']);

        Route::get('/roles', [RoleController::class, 'index']);
        Route::post('/roles', [RoleController::class, 'store']);
        Route::get('/roles/{role}', [RoleController::class, 'show']);
        Route::put('/roles/{role}', [RoleController::class, 'update']);
        Route::delete('/roles/{role}', [RoleController::class, 'destroy']);
        Route::post('/roles/{role}/assign-permissions', [RoleController::class, 'assignPermissions']);

        Route::get('/permissions', [PermissionController::class, 'index']);
        Route::post('/permissions', [PermissionController::class, 'store']);
        Route::get('/permissions/{permission}', [PermissionController::class, 'show']);
        Route::put('/permissions/{permission}', [PermissionController::class, 'update']);
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy']);
        Route::get('/permissions/groups', [PermissionController::class, 'getGroups']);
        Route::get('/permissions/all', [PermissionController::class, 'getAllPermissions']);

        Route::get('/activity-logs', [ActivityLogController::class, 'index']);
        Route::get('/activity-logs/{activity}', [ActivityLogController::class, 'show']);
        Route::get('/activity-logs/log-names', [ActivityLogController::class, 'getLogNames']);
        Route::get('/activity-logs/events', [ActivityLogController::class, 'getEvents']);

        Route::get('/admin/translations/{locale}', [AdminTranslationController::class, 'show']);
        Route::put('/admin/translations/{locale}', [AdminTranslationController::class, 'update']);
        
        // Services
        Route::apiResource('services', ServiceController::class);
        
        // Projects
        Route::apiResource('projects', ProjectController::class);
        
        // Testimonials
        Route::apiResource('testimonials', TestimonialController::class);
        
        // FAQs
        Route::apiResource('faqs', FaqController::class);
        
        // Settings
        Route::apiResource('settings', SettingController::class);

        // Email Templates
        Route::apiResource('email-templates', EmailTemplateController::class);

        // Contact Messages
        Route::apiResource('contact-messages', ContactMessageController::class);

        // Media
        Route::get('media/folders', [MediaController::class, 'folders']);
        Route::get('media/types', [MediaController::class, 'types']);
        Route::post('media/bulk-delete', [MediaController::class, 'bulkDestroy']);
        Route::apiResource('media', MediaController::class);
    });
});
