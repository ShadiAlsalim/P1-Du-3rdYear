<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobOppController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
});

//auth//
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/login', [AuthController::class, 'login']);  //has a code => middleware
Route::post('/forget-password', [AuthController::class, 'forgetPassword']); //if has and email
Route::post('/reset-password/{email}', [AuthController::class, 'resetPassword']);
Route::get('/resend-password/{email}', [AuthController::class, 'resendCode']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

//functions//
//post job
Route::post('/postJob', [JobOppController::class, 'create']);
//edit job
Route::put('/editJob', [JobOppController::class, 'edit']);
//show all jobs
Route::get('/indexJobs', [JobOppController::class, 'showAll']);
//show one job
Route::get('/showOne/{id}', [JobOppController::class, 'showSingle']);
//apply for a job 
Route::post('/apply/{id}', [JobOppController::class, 'apply']);
//cancel apllication for a job
Route::delete('/cancel/{id}', [JobOppController::class, 'cancel']);
//show user applications
Route::get('/myApp', [JobOppController::class, 'showApplications']);
//add job to favorites
Route::post('/addFav/{id}', [JobOppController::class, 'addFavorite']);
//remove job from favorites
Route::post('/removeFav/{id}', [JobOppController::class, 'removeFavorite']);
//show all companies
Route::get('/indexComps', [CompanyController::class, 'showAll']);
//show single company
Route::get('/showcomp/{id}', [CompanyController::class, 'showSingle']);
//follow company
Route::post('/followComp/{id}', [CompanyController::class, 'addFollow']);
//unfollow company
Route::post('/unfollowComp/{id}', [CompanyController::class, 'removeFollow']);