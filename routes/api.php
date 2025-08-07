<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfessorController;

Route::apiResource('professores', ProfessorController::class);
?>
