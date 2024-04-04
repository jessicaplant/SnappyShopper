<?php

use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::post('store', [StoreController::class, 'create']);

Route::get('store', [StoreController::class, 'findMany']);
