<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DealController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('properties', PropertyController::class);
    Route::post('/properties/{property}/images', [PropertyController::class, 'uploadImages'])->name('properties.images.upload');
    Route::delete('/properties/{property}/images/{image}', [PropertyController::class, 'deleteImage'])->name('properties.images.delete');
    
    Route::post('/properties/{property}/documents', [DocumentController::class, 'upload'])->name('documents.upload');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::put('/documents/{document}/verify', [DocumentController::class, 'verify'])->name('documents.verify');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    
    Route::resource('clients', ClientController::class);
    Route::resource('deals', DealController::class);
    
    Route::get('/settings', function () {
        return Inertia::render('Settings/Index');
    })->name('settings');
});