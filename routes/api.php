<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
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
    return $request->user();
});

Route::post('/login', [UserController::class, 'Login']);
Route::post('/registration', [UserController::class, 'Registration']);
Route::post('/logout', [UserController::class, 'Logout']);


Route::group(['middleware'=> []], function (){

});

Route::group(['middleware' => ['tokenVerify', 'admin']],function (){
    Route::get('/users', [UserController::class, 'Index']);
    Route::resource('/projects', ProjectController::class);
    Route::resource('/tasks', TaskController::class);
});

Route::group(['middleware' => ['tokenVerify']],function (){
    Route::get('projects', [ProjectController::class, 'index']);
    Route::get('user/projects/{id}', [ProjectController::class, 'projectDetails']);
    Route::get('tasks', [TaskController::class, 'index']);
    Route::PATCH('user/{id}/tasks', [TaskController::class, 'taskStatusUpdate']);
});


