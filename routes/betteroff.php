<?php

use Illuminate\Support\Facades\Route;

// Public BetterOff.FYI calculator — no login, no employee-level personal data collected.
Route::prefix('betteroff')->group(function () {
    Route::get('', 'BetterOff\\PublicCalculatorController@index')->name('betteroff.public.index');
    Route::post('calculate', 'BetterOff\\PublicCalculatorController@calculate')->name('betteroff.public.calculate');
    Route::post('calculate/candidate', 'BetterOff\\PublicCalculatorController@calculateCandidate')->name('betteroff.public.calculate.candidate');
    Route::get('share/{token}', 'BetterOff\\PublicCalculatorController@sharedCandidateView')->name('betteroff.candidate-share');

    // Search-intent landing pages, one per route, each built from live engine output.
    Route::get('cost-of-hiring-an-overseas-engineer-in-the-uk', 'BetterOff\\SearchLandingController@overseasEngineer')->name('betteroff.search.overseas-engineer');
});

// In-app cost panel, attached to the role/offer flow. Requires company context
// and at least HR permission (matches the existing compliance-item gate).
Route::middleware(['auth:sanctum', 'verified', 'company'])->prefix('{company}/betteroff')->group(function () {
    Route::middleware(['hr'])->group(function () {
        Route::get('', 'Company\\BetterOff\\CostPanelController@index')->name('betteroff.panel.index');
        Route::get('job-openings/{jobOpeningId}', 'Company\\BetterOff\\CostPanelController@index')->name('betteroff.panel.for-job-opening');
        Route::post('calculate', 'Company\\BetterOff\\CostPanelController@calculate')->name('betteroff.panel.calculate');
        Route::post('scenarios', 'Company\\BetterOff\\CostPanelController@store')->name('betteroff.panel.scenarios.store');
        Route::post('scenarios/{scenarioId}/share', 'Company\\BetterOff\\CostPanelController@share')->name('betteroff.panel.scenarios.share');
    });

    // Confirming a sponsored hire creates real compliance obligations — administrator only.
    Route::middleware(['administrator'])->group(function () {
        Route::post('sponsored-hires', 'Company\\BetterOff\\SponsoredHireController@store')->name('betteroff.sponsored-hires.store');
    });
});
