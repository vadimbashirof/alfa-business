<?php

use App\Http\Controllers\AlfaBusinessCallback;
use App\Http\Middleware\AlfaBusinessCallbackResponseMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => [AlfaBusinessCallbackResponseMiddleware::class]], static function () {
    Route::get('merchant/alfa-business/callback', [AlfaBusinessCallback::class, 'handle']);
    Route::post('merchant/alfa-business/callback', [AlfaBusinessCallback::class, 'handle']);
});
