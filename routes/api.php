<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

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

// User Routes
Route::post('/register',    [AuthController::class, 'register']);
Route::post('/login',       [AuthController::class, 'login']);
Route::post('/update/{id}', [AuthController::class, 'update']);
Route::post('/logout',      [AuthController::class, 'logout']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/me',               [AuthController::class, 'me']);

    // Post Routes
    Route::get('/post',             [PostController::class, 'index']);
    Route::post('/create',          [PostController::class, 'store']);
    Route::post('/update-post/{id}',[PostController::class, 'update']);
    Route::delete('/delete/{id}',   [PostController::class, 'destroy']);

    // like and dislike
    Route::get('/get-likes/{postId}',    [LikeController::class, 'getLikes']);
    Route::post('/toggle-like/{postId}', [LikeController::class, 'toggleLike']);

    // Comment Routes
    Route::get('/comments/{postId}',     [CommentController::class, 'showPostDetail']);
    Route::post('/comment/{id}',         [CommentController::class, 'store']);
    Route::post('/update-comment/{id}',  [CommentController::class, 'update']);
    Route::delete('/delet-comment/{id}', [CommentController::class, 'destroy']);
});

