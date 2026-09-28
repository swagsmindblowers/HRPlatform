<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::middleware(['company'])->prefix('{company}')->group(function () {
        // AI tools - the HTTP surface the standalone MCP server calls into.
        Route::get('ai-tools', 'Api\\AiToolsController@index');
        Route::post('ai-tools/{tool}/invoke', 'Api\\AiToolsController@invoke');
    });
});
