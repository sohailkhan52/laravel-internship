<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TimelogController;
// Public routes
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working!',
        'timestamp' => now()->toDateTimeString()
    ]);
});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);




Route::middleware(['auth:api'])->group(function () {

    Route::post('/index', [TicketController::class, 'index']);
    Route::post('/startTicket', [TicketController::class, 'startTicket']);
    Route::post('/openTicket', [TicketController::class, 'openTicket']);
    Route::post('/closeTicket', [TicketController::class, 'closeTicket']);
    Route::post('/checklist', [TicketController::class, 'checklist']);

    // TimeLog routes 
Route::get("/time-log/{ticket}/start",[TimelogController::class,'start'])->name('timee-log.start');
Route::get("/time-log/{ticket}/stop",[TimelogController::class,'stop'])->name('timee-log.stop');


    Route::middleware(['role:admin'])->group(function () {
    Route::post('/createTickets', [TicketController::class, 'createTicket']);
    Route::post("/userDuration",[TimelogController::class,'userDuration']);
    Route::post("/userDuration/pdf",[TimelogController::class,'userDurationPdf']);
});
    });





?>