<?php

use App\Http\Controllers\Api\V1\ApplicationController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\LikeController;
use App\Http\Controllers\Api\V1\MetaController;
use App\Http\Controllers\Api\V1\ResumeController;
use App\Http\Controllers\Api\V1\VacancyController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('auth/register', [AuthController::class, 'register']);
        Route::post('auth/login', [AuthController::class, 'login']);
    });

    Route::get('meta', MetaController::class);
    Route::get('vacancies', [VacancyController::class, 'index']);
    Route::get('vacancies/{vacancy}', [VacancyController::class, 'show'])->whereNumber('vacancy');
    Route::get('companies', [CompanyController::class, 'index']);
    Route::get('companies/{company}', [CompanyController::class, 'show'])->whereNumber('company');
    Route::get('{type}/{id}/comments', [CommentController::class, 'index'])
        ->whereIn('type', ['vacancies', 'resumes'])->whereNumber('id');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::put('auth/profile', [AuthController::class, 'updateProfile']);

        Route::post('{type}/{id}/comments', [CommentController::class, 'store'])
            ->whereIn('type', ['vacancies', 'resumes'])->whereNumber('id');
        Route::put('comments/{comment}', [CommentController::class, 'update']);
        Route::delete('comments/{comment}', [CommentController::class, 'destroy']);

        Route::post('{type}/{id}/like', [LikeController::class, 'toggle'])
            ->whereIn('type', ['vacancies', 'resumes'])->whereNumber('id');
        Route::get('me/saved', [LikeController::class, 'saved']);

        Route::get('resumes', [ResumeController::class, 'index']);
        Route::get('resumes/{resume}', [ResumeController::class, 'show'])->whereNumber('resume');

        Route::middleware('role:seeker')->group(function () {
            Route::get('me/resumes', [ResumeController::class, 'mine']);
            Route::post('resumes', [ResumeController::class, 'store']);
            Route::put('resumes/{resume}', [ResumeController::class, 'update']);
            Route::delete('resumes/{resume}', [ResumeController::class, 'destroy']);

            Route::post('vacancies/{vacancy}/apply', [ApplicationController::class, 'store']);
            Route::get('me/applications', [ApplicationController::class, 'mine']);
            Route::delete('applications/{application}', [ApplicationController::class, 'destroy']);
        });

        Route::middleware('role:employer')->group(function () {
            Route::get('me/company', [CompanyController::class, 'mine']);
            Route::put('me/company', [CompanyController::class, 'update']);

            Route::get('me/vacancies', [VacancyController::class, 'mine']);
            Route::post('vacancies', [VacancyController::class, 'store']);
            Route::put('vacancies/{vacancy}', [VacancyController::class, 'update']);
            Route::delete('vacancies/{vacancy}', [VacancyController::class, 'destroy']);

            Route::get('me/incoming-applications', [ApplicationController::class, 'incoming']);
            Route::get('vacancies/{vacancy}/applications', [ApplicationController::class, 'forVacancy']);
            Route::patch('applications/{application}', [ApplicationController::class, 'updateStatus']);
        });
    });
});
