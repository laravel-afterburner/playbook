<?php

use Afterburner\Playbook\Http\Controllers\PlaybookController;
use Afterburner\Playbook\Support\HelpSupportRoute;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::get(HelpSupportRoute::uri(), [PlaybookController::class, 'index'])
        ->name('playbook.index');

    Route::get(HelpSupportRoute::uri('{section}'), [PlaybookController::class, 'section'])
        ->name('playbook.section');

    Route::get(HelpSupportRoute::uri('{section}', '{page}'), [PlaybookController::class, 'show'])
        ->name('playbook.show');
});
