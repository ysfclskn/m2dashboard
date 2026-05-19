<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\ProjectProgressReportController;
use App\Http\Controllers\ProjectSprintController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\ProjectWikiPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Projects
    Route::resource('projects', ProjectController::class);

    // Project-scoped routes
    Route::prefix('projects/{project}')->name('projects.')->group(function () {

        // Quests (Tasks)
        Route::get('quests', [ProjectTaskController::class, 'index'])->name('quests.index');
        Route::get('quests/create', [ProjectTaskController::class, 'create'])->name('quests.create');
        Route::post('quests', [ProjectTaskController::class, 'store'])->name('quests.store');
        Route::get('quests/{task}', [ProjectTaskController::class, 'show'])->name('quests.show');
        Route::get('quests/{task}/edit', [ProjectTaskController::class, 'edit'])->name('quests.edit');
        Route::put('quests/{task}', [ProjectTaskController::class, 'update'])->name('quests.update');
        Route::patch('quests/{task}/status', [ProjectTaskController::class, 'updateStatus'])->name('quests.status');
        Route::delete('quests/{task}', [ProjectTaskController::class, 'destroy'])->name('quests.destroy');

        // Sprints (Raids)
        Route::resource('sprints', ProjectSprintController::class)->except(['show']);
        Route::get('sprints/{sprint}', [ProjectSprintController::class, 'show'])->name('sprints.show');

        // Wiki
        Route::resource('wiki', ProjectWikiPageController::class)
            ->parameters(['wiki' => 'wikiPage']);

        // Progress Reports
        Route::resource('reports', ProjectProgressReportController::class)
            ->parameters(['reports' => 'progressReport']);

        // Members
        Route::get('members', [ProjectMemberController::class, 'index'])->name('members.index');
        Route::post('members', [ProjectMemberController::class, 'store'])->name('members.store');
        Route::patch('members/{projectMember}', [ProjectMemberController::class, 'update'])->name('members.update');
        Route::delete('members/{projectMember}', [ProjectMemberController::class, 'destroy'])->name('members.destroy');
    });
});

require __DIR__.'/auth.php';
