<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VoteController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Middleware\EnsureUserIsOrganizer;

Route::post('/votes', [VoteController::class, 'store']);
Route::get('/votes/count', [VoteController::class, 'count']);
Route::get('/votes/{electionId}/{index}', [VoteController::class, 'show'])
	->where(['index' => '[0-9]+']);

// Contract deployment (organizer only)
Route::post('/contract/deploy', [ContractController::class, 'deploy'])
	->middleware(['auth', EnsureUserIsOrganizer::class]);
