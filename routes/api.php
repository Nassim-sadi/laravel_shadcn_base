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
use App\Http\Controllers\Api\BlogPostController;
use App\Http\Controllers\Api\BlogCategoryController;
use App\Http\Controllers\Api\BlogTagController;
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

        Route::get('/permissions/all', [PermissionController::class, 'getAllPermissions']);

        // Activity Logs
        if (config('modules.activity_logs', true)) {
            Route::get('/activity-logs', [ActivityLogController::class, 'index']);
            Route::get('/activity-logs/{activity}', [ActivityLogController::class, 'show']);
            Route::get('/activity-logs/log-names', [ActivityLogController::class, 'getLogNames']);
            Route::get('/activity-logs/events', [ActivityLogController::class, 'getEvents']);
        }

        if (config('modules.translations', true)) {
            Route::get('/admin/translations/{locale}', [AdminTranslationController::class, 'show']);
            Route::put('/admin/translations/{locale}', [AdminTranslationController::class, 'update']);
        }
        
        // Services
        if (config('modules.services', true)) {
            Route::apiResource('services', ServiceController::class);
        }

        // Projects
        if (config('modules.projects', true)) {
            Route::apiResource('projects', ProjectController::class);
        }

        // Testimonials
        if (config('modules.testimonials', true)) {
            Route::apiResource('testimonials', TestimonialController::class);
        }

        // FAQs
        if (config('modules.faqs', true)) {
            Route::apiResource('faqs', FaqController::class);
        }

        // Settings
        Route::apiResource('settings', SettingController::class);

        // Email Templates
        if (config('modules.email_templates', true)) {
            Route::apiResource('email-templates', EmailTemplateController::class);
        }

        // Contact Messages
        if (config('modules.contact', true)) {
            Route::apiResource('contact-messages', ContactMessageController::class);
        }

        // Blog Module
        if (config('modules.blog', true)) {
            Route::apiResource('blog-posts', BlogPostController::class);
            Route::apiResource('blog-categories', BlogCategoryController::class);
            Route::get('blog-tags', [BlogTagController::class, 'index']);
            Route::post('blog-tags', [BlogTagController::class, 'store']);
            Route::delete('blog-tags/{blogTag}', [BlogTagController::class, 'destroy']);
        }

        // Media
        if (config('modules.media', true)) {
            Route::get('media/folders', [MediaController::class, 'folders']);
            Route::get('media/types', [MediaController::class, 'types']);
            Route::post('media/bulk-delete', [MediaController::class, 'bulkDestroy']);
            Route::apiResource('media', MediaController::class);
        }
    });
});