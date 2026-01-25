<?php

use App\Http\Controllers\Api\AiFinanceController;


    Route::get('/ai/finance/summary', [AiFinanceController::class, 'summary']);
    Route::get('/ai/finance/details', [AiFinanceController::class, 'details']);
    Route::get('/ai/finance/context/{userId}', [AiFinanceController::class, 'context']);

