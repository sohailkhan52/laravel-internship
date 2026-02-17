<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\DragController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\TimeLogController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();


Route::get('/profile', [ProfileController::class, 'index']);
Route::post('/change_password', [ProfileController::class, 'change_password']);

// Google Login Routes
Route::get('/auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);


Route::get('/home', [HomeController::class, 'index'])->name('home');
// admin route 
Route::middleware(['role:admin'])->group(function () {
    Route::get('/home',  [HomeController::class, 'index']);
    Route::post('/createTicket',  [HomeController::class, 'createTicket']);
    Route::post('/createboard',  [BoardController::class, 'createBoard']);
    
Route::post("/user_duration",[TimeLogController::class,'userDuration']);
Route::get('/timelog/export', [TimeLogController::class, 'exportPdf'])->name('timelog.export');

    
});




// TimeLog routes 
Route::get("/time-log/{ticket}/start",[TimeLogController::class,'start'])->name('time-log.start');
Route::get("/time-log/{ticket}/stop",[TimeLogController::class,'stop'])->name('time-log.stop');


    //checklist route
    Route::get('/board/checklist/{checklist}', [HomeController::class, 'checklist']);
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');


//These routes  controle the points and working progress of the task
Route::post('/tickets/{ticket}/start', [HomeController::class, 'start']);
Route::post('/tickets/{ticket}/open', [HomeController::class, 'open']);
Route::post('/tickets/{ticket}/close', [HomeController::class, 'close']);


Route::get('/board/{board_id}', [BoardController::class, 'index']);


// routes for drag and drop 
Route::post('/ticket/{ticket}/start', [DragController::class, 'start']);
Route::post('/ticket/{ticket}/open', [DragController::class, 'open']);
Route::post('/ticket/{ticket}/closeTicket', [DragController::class, 'closeTicket']);
//drag and drop in home page 
Route::post('/tickets/{ticket}/status', [App\Http\Controllers\HomeController::class, 'updateStatus'])->name('tickets.updateStatus');

//NOTIFICATION ROUTES
Route::get('/notifications', fn () =>view('notifications.index', ['notifications' => auth()->user()->notifications]))->middleware('auth');
// Route::post('/notifications/mark-read', function () {auth()->user()->unreadNotifications->markAsRead();return response()->json(['status' => 'ok']);})->middleware('auth');
// routes/web.php
Route::post('/notifications/mark-read', function() {$user = auth()->user();
$user->unreadNotifications->markAsRead(); return response()->json(['success' => true]);})->name('notifications.mark-read');



