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
