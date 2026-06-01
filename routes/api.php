<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AiContentController;
use App\Http\Controllers\Api\AiImportController;
use App\Http\Controllers\Api\AiSettingsController;
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
use App\Http\Controllers\Api\CatalogCategoryController;
use App\Http\Controllers\Api\CatalogProductController;
use App\Http\Controllers\Api\CatalogTagController;
use App\Http\Controllers\Api\CatalogAttributeController;
use App\Http\Controllers\Api\CatalogMarqueeController;
use App\Http\Controllers\Api\CatalogBrandController;
use App\Http\Controllers\Api\BookingServiceController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\BookingAvailabilityController;
use App\Http\Controllers\Api\BookingSettingsController;
use App\Http\Controllers\Api\QuoteRequestController;
use Illuminate\Support\Facades\Route;

Route::middleware(['json.response', 'throttle.api'])->group(function () {
    Route::get('/localization', [LocalizationController::class, 'index']);
    Route::get('/translations/{locale}', [LocalizationController::class, 'translations']);

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
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

        // Settings & AI — core, no module gating
        Route::apiResource('settings', SettingController::class);
        Route::get('/ai/settings', [AiSettingsController::class, 'show']);
        Route::put('/ai/settings', [AiSettingsController::class, 'update']);
        Route::post('/ai/generate-content', [AiContentController::class, 'generate']);
        Route::post('/ai/import-content/preview', [AiImportController::class, 'preview']);
        Route::post('/ai/import-content/confirm', [AiImportController::class, 'confirm']);

        // ── Module-gated routes ──

        Route::middleware('module:activity_logs')->group(function () {
            Route::get('/activity-logs', [ActivityLogController::class, 'index']);
            Route::get('/activity-logs/{activity}', [ActivityLogController::class, 'show']);
            Route::get('/activity-logs/log-names', [ActivityLogController::class, 'getLogNames']);
            Route::get('/activity-logs/events', [ActivityLogController::class, 'getEvents']);
        });

        Route::middleware('module:translations')->group(function () {
            Route::get('/admin/translations/{locale}', [AdminTranslationController::class, 'show']);
            Route::put('/admin/translations/{locale}', [AdminTranslationController::class, 'update']);
        });

        Route::middleware('module:services')->group(function () {
            Route::apiResource('services', ServiceController::class);
            Route::post('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus']);
        });

        Route::middleware('module:projects')->group(function () {
            Route::apiResource('projects', ProjectController::class);
            Route::post('projects/{project}/toggle-status', [ProjectController::class, 'toggleStatus']);
        });

        Route::middleware('module:testimonials')->group(function () {
            Route::apiResource('testimonials', TestimonialController::class);
            Route::post('testimonials/{testimonial}/toggle-status', [TestimonialController::class, 'toggleStatus']);
        });

        Route::middleware('module:faqs')->group(function () {
            Route::apiResource('faqs', FaqController::class);
            Route::post('faqs/{faq}/toggle-status', [FaqController::class, 'toggleStatus']);
        });

        Route::middleware('module:email_templates')->group(function () {
            Route::apiResource('email-templates', EmailTemplateController::class);
            Route::post('email-templates/{email_template}/toggle-status', [EmailTemplateController::class, 'toggleStatus']);
        });

        Route::middleware('module:contact')->group(function () {
            Route::apiResource('contact-messages', ContactMessageController::class);
            Route::post('contact-messages/bulk-delete', [ContactMessageController::class, 'bulkDestroy']);
            Route::post('contact-messages/{contact_message}/toggle-status', [ContactMessageController::class, 'toggleStatus']);
        });

        Route::middleware('module:blog')->group(function () {
            Route::apiResource('blog-posts', BlogPostController::class);
            Route::post('blog-posts/{blog_post}/toggle-status', [BlogPostController::class, 'toggleStatus']);
            Route::apiResource('blog-categories', BlogCategoryController::class);
            Route::get('blog-tags', [BlogTagController::class, 'index']);
            Route::post('blog-tags', [BlogTagController::class, 'store']);
            Route::delete('blog-tags/{blogTag}', [BlogTagController::class, 'destroy']);
        });

        Route::middleware('module:media')->group(function () {
            Route::get('media/folders', [MediaController::class, 'folders']);
            Route::get('media/types', [MediaController::class, 'types']);
            Route::post('media/bulk-delete', [MediaController::class, 'bulkDestroy']);
            Route::apiResource('media', MediaController::class);
        });

        Route::middleware('module:catalog')->group(function () {
            Route::get('catalog-categories/all', [CatalogCategoryController::class, 'all']);
            Route::apiResource('catalog-categories', CatalogCategoryController::class);
            Route::post('catalog-categories/{catalogCategory}/toggle-status', [CatalogCategoryController::class, 'toggleStatus']);

            Route::get('catalog-brands/all', [CatalogBrandController::class, 'all']);
            Route::apiResource('catalog-brands', CatalogBrandController::class);
            Route::post('catalog-brands/{catalogBrand}/toggle-status', [CatalogBrandController::class, 'toggleStatus']);

            Route::apiResource('catalog-products', CatalogProductController::class);
            Route::post('catalog-products/{catalogProduct}/toggle-status', [CatalogProductController::class, 'toggleStatus']);

            Route::get('catalog-tags', [CatalogTagController::class, 'index']);
            Route::post('catalog-tags', [CatalogTagController::class, 'store']);
            Route::delete('catalog-tags/{catalogTag}', [CatalogTagController::class, 'destroy']);

            Route::get('catalog-attributes', [CatalogAttributeController::class, 'index']);
            Route::post('catalog-attributes', [CatalogAttributeController::class, 'store']);
            Route::put('catalog-attributes/{catalogAttribute}', [CatalogAttributeController::class, 'update']);
            Route::delete('catalog-attributes/{catalogAttribute}', [CatalogAttributeController::class, 'destroy']);

            Route::get('catalog-marquee', [CatalogMarqueeController::class, 'index']);
            Route::post('catalog-marquee', [CatalogMarqueeController::class, 'store']);
            Route::put('catalog-marquee/{catalogMarqueeItem}', [CatalogMarqueeController::class, 'update']);
            Route::delete('catalog-marquee/{catalogMarqueeItem}', [CatalogMarqueeController::class, 'destroy']);

            Route::get('quote-requests', [QuoteRequestController::class, 'index']);
            Route::get('quote-requests/{quoteRequest}', [QuoteRequestController::class, 'show']);
            Route::post('quote-requests/{quoteRequest}/reply', [QuoteRequestController::class, 'reply']);
            Route::delete('quote-requests/{quoteRequest}', [QuoteRequestController::class, 'destroy']);
            Route::post('quote-requests/bulk-delete', [QuoteRequestController::class, 'bulkDestroy']);
        });

        Route::middleware('module:booking')->group(function () {
            Route::get('booking-services/all', [BookingServiceController::class, 'all']);
            Route::apiResource('booking-services', BookingServiceController::class);
            Route::post('booking-services/{bookingService}/toggle-status', [BookingServiceController::class, 'toggleStatus']);
            Route::post('booking-services/{bookingService}/time-blocks', [BookingServiceController::class, 'storeTimeBlock']);
            Route::delete('booking-services/{bookingService}/time-blocks/{timeBlock}', [BookingServiceController::class, 'destroyTimeBlock']);

            Route::get('bookings', [BookingController::class, 'index']);
            Route::get('bookings/{booking}', [BookingController::class, 'show']);
            Route::post('bookings/{booking}/confirm', [BookingController::class, 'confirm']);
            Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel']);
            Route::post('bookings/{booking}/complete', [BookingController::class, 'complete']);
            Route::post('bookings/{booking}/reschedule', [BookingController::class, 'reschedule']);
            Route::delete('bookings/{booking}', [BookingController::class, 'destroy']);
            Route::post('bookings/bulk-delete', [BookingController::class, 'bulkDestroy']);

            Route::get('booking-services/{bookingService}/availability', [BookingAvailabilityController::class, 'index']);

            Route::get('booking-settings', [BookingSettingsController::class, 'index']);
            Route::put('booking-settings', [BookingSettingsController::class, 'update']);
        });
    });
});

// Public catalog routes (no auth required)
Route::middleware(['json.response', 'throttle.api', 'module:catalog'])->group(function () {
    Route::get('catalog-marquee/public', [CatalogMarqueeController::class, 'publicIndex']);
    Route::post('quote-requests', [QuoteRequestController::class, 'store']);
});
