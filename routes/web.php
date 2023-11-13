<?php

use App\Http\Controllers\ChannelController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HookController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;


Route::post('/webhook/telegram',[HookController::class , 'getMessage']);

Route::get('/', function (){
    return redirect(route('login page'));
});

Route::get('/login',[Controller::class,'loginPage'])->name('login page');
Route::get('/code/{email}',[Controller::class,'codePage'])->name('code page');

Route::post('/login', [Controller::class,'login'])->name('login');
Route::post('/checkCode',[Controller::class,'checkCode'])->name('check code');

Route::group(['prefix' => 'app'], function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/channels', [ChannelController::class, 'getMy'])->name('index');
        Route::post('/addChannel', [ChannelController::class, 'add'])->name('add channel');
        Route::get('/channel/{id}/messages', [ChannelController::class, 'getMessages'])->name('channel page');
        Route::delete('/channel/{id}/delete/{message}', [ChannelController::class, 'delete']);
        Route::post('/channel/{id}/restore/{message}', [MessageController::class, 'sendMessage'])->name('restore message');
        Route::post('/channel/{id}/send', [MessageController::class, 'send']);
        Route::post('/message/{message}/channel/{id}', [MessageController::class, 'sendMessage']);
    });
});
