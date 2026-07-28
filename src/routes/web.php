<?php

use App\Http\Controllers\DailyGoalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\StudyScheduleController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

// Redireciona para login (guest) ou dashboard (auth)
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Dashboard (revisões do dia) — autenticado
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Meta do Dia (tópicos a revisar hoje)
Route::get('/meta-do-dia', DailyGoalController::class)
    ->middleware(['auth', 'verified'])
    ->name('daily-goal');

Route::middleware('auth')->group(function () {
    // Perfil do usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Disciplinas (CRUD sem create/edit — usamos modais Alpine.js)
    Route::resource('subjects', SubjectController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    // Tópicos aninhados em disciplinas
    Route::resource('subjects.topics', TopicController::class)
        ->only(['show', 'store', 'update', 'destroy'])
        ->shallow();

    // Recursos de tópicos
    Route::post('topics/{topic}/resources', [ResourceController::class, 'store'])
        ->name('topics.resources.store');
    Route::delete('resources/{resource}', [ResourceController::class, 'destroy'])
        ->name('resources.destroy');

    // Agendamento de revisões
    Route::patch('schedules/{schedule}/review', [StudyScheduleController::class, 'review'])
        ->name('schedules.review');
    Route::patch('schedules/{schedule}/interval', [StudyScheduleController::class, 'updateInterval'])
        ->name('schedules.interval');
    Route::patch('schedules/{schedule}/next-review', [StudyScheduleController::class, 'updateNextReview'])
        ->name('schedules.next-review');

    // Favoritos (toggle)
    Route::post('favorites/toggle', [FavoriteController::class, 'toggle'])
        ->name('favorites.toggle');

    // Relatórios
    Route::get('reports', [ReportController::class, 'index'])
        ->name('reports.index');
});

require __DIR__.'/auth.php';
