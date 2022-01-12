<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
   Route::get('greet', function () {
       return "Hello user";
   });
});

Route::post('user/register', [UsersController::class, 'store'])->name('create.user');
Route::post('user/login', [UsersController::class, 'login'])->name('login.user');