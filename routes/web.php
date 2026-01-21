<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketAttachmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/toast-test', function () {
    session()->flash('success', 'Toast OK — UI system online.');
    return redirect()->route('dashboard');
})->middleware(['auth', 'verified'])->name('toast.test');

Route::get('/toast-error-test', function () {
    session()->flash('error', 'Toast error — check the UI system.');
    return redirect()->route('dashboard');
})->middleware(['auth', 'verified'])->name('toast.error.test');

Route::middleware('auth')->group(function () {
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::patch('/tickets/{ticket}/assign', [TicketController::class, 'assignToMe'])->name('tickets.assign');
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.status');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/comments', [TicketCommentController::class, 'store'])->name('tickets.comments.store');
    Route::post('/tickets/{ticket}/attachments', [TicketAttachmentController::class, 'store'])
        ->name('tickets.attachments.store');
    Route::get('/tickets/{ticket}/attachments/{attachment}', [TicketAttachmentController::class, 'download'])
        ->name('tickets.attachments.download');
    Route::delete('/tickets/{ticket}/attachments/{attachment}', [TicketAttachmentController::class, 'destroy'])
        ->name('tickets.attachments.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
