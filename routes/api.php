<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\FaqQuestionController;
use App\Http\Controllers\Api\ClientImageController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\JobOpeningController;
use App\Http\Controllers\Api\BrochureRequestController;
use App\Http\Controllers\Api\PostController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/contact-us', [ContactController::class, 'store']);
Route::post('/faq-questions', [FaqQuestionController::class, 'store']);
Route::get('/client-images', [ClientImageController::class, 'index']);
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/reviews', [ReviewController::class, 'index']);
Route::get('/job-openings', [JobOpeningController::class, 'index']);
Route::post('/job-applications', [JobOpeningController::class, 'apply']);
Route::post('/track-visit', [AnalyticsController::class, 'trackVisit']);
Route::post('/leave-page', [AnalyticsController::class, 'leavePage']);
Route::post('/brochure-requests', [BrochureRequestController::class, 'store']);

// Blog System Routes
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/tags/all', [PostController::class, 'tags']);
Route::get('/posts/{slug}', [PostController::class, 'show']);
Route::post('/admin/posts', [PostController::class, 'store']);
Route::put('/admin/posts/{id}', [PostController::class, 'update']);
Route::delete('/admin/posts/{id}', [PostController::class, 'destroy']);
