<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ExpenseController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

// Reports
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/weekly', [ReportController::class, 'weekly'])->name('weekly');
    Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
    Route::get('/yearly', [ReportController::class, 'yearly'])->name('yearly');
    Route::get('/export/{type}', [ReportController::class, 'export'])->name('export');
});

// Resources
Route::resource('companies', CompanyController::class);
Route::resource('projects', ProjectController::class);
Route::resource('tasks', TaskController::class);
Route::resource('achievements', AchievementController::class);
Route::resource('budgets', BudgetController::class);
Route::resource('expenses', ExpenseController::class);
