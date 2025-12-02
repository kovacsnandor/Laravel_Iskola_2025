<?php

use App\Http\Controllers\PlayingsportController;
use App\Http\Controllers\SchoolclassController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//endpoint
Route::get('/x', function(){
    return 'API';
});


//region users
//User kezelés, login, logout
//Mindenki
Route::post('users/login', [UserController::class, 'login']);
Route::post('users/logout', [UserController::class, 'logout']);
Route::post('users', [UserController::class, 'store']);

//Admin: 
//minden user lekérdezése
Route::get('users', [UserController::class, 'index'])
    ->middleware('auth:sanctum', 'ability:admin');
//Egy user lekérése    
Route::get('users/{id}', [UserController::class, 'show'])
    ->middleware('auth:sanctum', 'ability:admin');
//User adatok módosítása      
Route::patch('users/{id}', [UserController::class, 'update'])
->middleware('auth:sanctum', 'ability:admin');
//User törlés
Route::delete('users/{id}', [UserController::class, 'destroy'])
->middleware('auth:sanctum', 'ability:admin');  

//User self (Amit a user önmagával csinálhat) parancsok
Route::delete('usersme', [UserController::class, 'destroySelf'])
->middleware('auth:sanctum', 'ability:usersme:delete');

Route::patch('usersme', [UserController::class, 'updateSelf'])
->middleware('auth:sanctum', 'ability:usersme:patch');

Route::get('usersme', [UserController::class, 'indexSelf'])
    ->middleware('auth:sanctum', 'ability:usersme:get'); 
//endregion

//region Students
Route::get('students', [StudentController::class, 'index']);
Route::get('students/{id}', [StudentController::class, 'show']);
Route::post('students', [StudentController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:students:post']);
Route::patch('students/{id}', [StudentController::class, 'update'])
    ->middleware(['auth:sanctum','ability:students:patch']);
Route::delete('students/{id}', [StudentController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:students:delete']);
//endregion

//region Playingsport
Route::get('playingsports', [PlayingsportController::class, 'index']);
Route::get('playingsports/{id}', [PlayingsportController::class, 'show']);
Route::post('playingsports', [PlayingsportController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:playingsports:post']);
Route::patch('playingsports/{id}', [PlayingsportController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:playingsports:patch']);
Route::delete('playingsports/{id}', [PlayingsportController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:playingsports:delete']);
//endregion

//region Schoolclass
Route::get('schoolclasses', [SchoolclassController::class, 'index']);
Route::get('schoolclasses/{id}', [SchoolclassController::class, 'show']);
Route::post('schoolclasses', [SchoolclassController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:schoolclasses:post']);
Route::patch('schoolclasses/{id}', [SchoolclassController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:schoolclasses:patch']);
Route::delete('schoolclasses/{id}', [SchoolclassController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:schoolclasses:delete']);
//endregion

//region Sport
Route::get('sports', [SportController::class, 'index']);
Route::get('sports/{id}', [SportController::class, 'show']);
Route::post('sports', [SportController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:sports:post']);
Route::patch('sports/{id}', [SportController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:sports:patch']);
Route::delete('sports/{id}', [SportController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:sports:delete']);
//endregion