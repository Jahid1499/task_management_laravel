<?php

use App\Http\Controllers\ProjectController;
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
//    Route::get('home', [AdminController::class, 'index'])->name('dashboard');                // Name route
//    Route::get('post/tag/{id}', [HomeController::class, 'tagposts'])->name('tagpost');       // Name route

    Route::get('/users', [UserController::class, 'Index']);
    Route::resource('/projects', ProjectController::class);
});

Route::group(['middleware' => ['tokenVerify']],function (){
    Route::get('user/projects', [ProjectController::class, 'Index']);
});


