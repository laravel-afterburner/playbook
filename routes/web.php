<?php

use Afterburner\Playbook\Http\Controllers\PlaybookController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::get('/playbook', [PlaybookController::class, 'index'])
        ->name('playbook.index');

    Route::get('/playbook/{section}', [PlaybookController::class, 'section'])
        ->name('playbook.section');

    Route::get('/playbook/{section}/{page}', [PlaybookController::class, 'show'])
        ->name('playbook.show');
});
