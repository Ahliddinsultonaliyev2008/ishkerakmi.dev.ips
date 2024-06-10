<?php

use App\Http\Controllers\Auth\LoginCompanyController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterCompanyController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\jobsComment;
use App\Http\Controllers\jobslike;
use App\Http\Controllers\likecontroller;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResumeCantroller;
use App\Http\Controllers\ResumeComment;
use App\Http\Controllers\jobsCompanyCantroller;
use App\Http\Controllers\resumelike;
use App\Http\Controllers\userlike;
use Illuminate\Support\Facades\Route;


Route::post('register', [RegisterController::class, 'register'])->name('re');
Route::post('login', [LoginController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('profile', [ProfileController::class, 'profile'])->name('profile');
    Route::post('profile/logout', [ProfileController::class, 'logout'])->name('profile.logout');


    Route::post('resume/store', [ResumeCantroller::class, 'store'])->name('resume.store');
    Route::get('resume/{id}', [ResumeCantroller::class, 'show'])->name('resume.show');
    Route::get('resume', [ResumeCantroller::class, 'all'])->name('resume.all');
    Route::delete('resume/{id}', [ResumeCantroller::class, 'destroy'])->name('resume.destroy');
    Route::put('resume/{id}', [ResumeCantroller::class, 'update'])->name('resume.update');


    Route::get('comment/resume', [ResumeComment::class, 'index'])->name('resume.comment.index');
    Route::post('comment/resume', [ResumeComment::class, 'store'])->name('resume.comment.store');
    Route::put('comment/resume/{id}', [ResumeComment::class, 'update'])->name('resume.comment.update');
    Route::delete('comment/resume/{id}', [ResumeComment::class, 'destroy'])->name('resume.comment.destroy');

    Route::post('like/resume', [resumelike::class, 'store'])->name('resume.like.store');
    Route::delete('like/resume/{id}', [resumelike::class, 'destroy'])->name('resume.like.destroy');
});

Route::post('register/company', [RegisterCompanyController::class, 'registercompany'])->name('register.company');
Route::post('login', [LoginCompanyController::class, 'logincompany'])->name('login.company');


Route::middleware(['beckauth'])->prefix('company')->name('company')->group(callback: function (){
    Route::post('profile', [CompanyProfileController::class, 'profilecompany'])->name('profile.company');

    Route::get('resume/{id}', [jobsCompanyCantroller::class, 'show'])->name('resume.company.show');
    Route::post('resume/store', [jobsCompanyCantroller::class, 'store'])->name('resume.company.store');
    Route::get('resume', [jobsCompanyCantroller::class, 'all'])->name('resume.company.all');
    Route::delete('resume/{id}', [jobsCompanyCantroller::class, 'destroy'])->name('resume.company.destroy');
    Route::put('resume/{id}', [jobsCompanyCantroller::class, 'update'])->name('resume.company.update');

    Route::get('comment/job', [jobsComment::class, 'index'])->name('jobs.comment.index');
    Route::post('comment/job', [jobsComment::class, 'store'])->name('jobs.comment.store');
    Route::put('comment/job/{id}', [jobsComment::class, 'update'])->name('jobs.comment.update');
    Route::delete('comment/job/{id}', [jobsComment::class, 'destroy'])->name('jobs.comment.destroy');

    Route::post('like/job', [jobslike::class, 'store'])->name('job.like.store');
    Route::delete('like/job/{id}', [jobslike::class, 'destroy'])->name('job.like.destroy');
});
