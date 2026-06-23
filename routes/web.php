<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WasteController;

Route::get('/', [WasteController::class, 'index'])->name('home');

Route::post('/detect-upload', [WasteController::class, 'detectUpload'])->name('detect.upload');

Route::post('/detect-camera', [WasteController::class, 'detectCamera'])->name('detect.camera');