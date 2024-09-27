<?php

use App\Http\Controllers\RelatorioPontoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RelatorioPontoController::class, 'tela']);


Route::post('/', [RelatorioPontoController::class, 'gerarRelatorio']);
