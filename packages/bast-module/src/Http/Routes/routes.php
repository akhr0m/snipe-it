<?php

use BastModule\Http\Controllers\BastReportController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => config('bast.routes.middleware', ['auth'])], function () {
    Route::get('users/{user}/bast-report', [BastReportController::class, 'getBastReport'])->name('users.bast_report');
    Route::post('users/{user}/bast-report/print', [BastReportController::class, 'storeAndPrintBastReport'])->name('users.bast_report.print');

    Route::get('bast-report/find', [BastReportController::class, 'findBastReport'])->name('bast.find');
    Route::get('bast-report/search', [BastReportController::class, 'showBastSearchPage'])->name('bast.search');
    Route::get('bast-report/check', [BastReportController::class, 'checkBastExists'])->name('bast.check');
    Route::get('bast-report/view/{id}', [BastReportController::class, 'viewBastReportById']);
    Route::get('bast-report/api-data', [BastReportController::class, 'getBastDataApi'])->name('bast.api.data');
});
