<?php

use App\Http\Controllers\FlaskProxyController;
use Illuminate\Support\Facades\Route;

Route::match(['GET', 'POST', 'OPTIONS'], '/{path?}', FlaskProxyController::class)
    ->where('path', '.*')
    ->name('flask.proxy');