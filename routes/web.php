<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;

use function Clue\StreamFilter\fun;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('testNotification',[UserController::class,'noti']);

Route::get('createCV', 'UserController@createCV');

Route::middleware('web')->group(function () {
});
Route::get('login-google', [SocialAuthController::class, 'redirectToProvider']);
Route::get('auth/google/callback', [SocialAuthController::class, 'handleCallback']);


Route::get('test', function() {
    $array = [1, 2, 3];
    $array2 = [2, 4];
    return array_diff_assoc($array, $array2);
});

Route::post('firebase', 'FirebaseController@index');


Route::get('/lang/{lang}',[\App\Http\Controllers\LangController::class,'setLang']);
