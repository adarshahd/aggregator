<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("v1/register", "App\Http\Controllers\V1\LoginController@register");

Route::post("v1/token", "App\Http\Controllers\V1\LoginController@getToken");

Route::middleware("auth:sanctum")
    ->prefix("v1")
    ->group(function () {
        Route::apiResource(
            "/articles",
            App\Http\Controllers\V1\ArticleController::class
        )->only("index");

        Route::apiResource(
            "articles/sources",
            App\Http\Controllers\V1\ArticleSourceController::class
        )->only("index");

        Route::apiResource(
            "articles/authors",
            App\Http\Controllers\V1\ArticleAuthorController::class
        )->only("index");

        Route::apiResource(
            "preferences",
            App\Http\Controllers\V1\PreferenceController::class
        );
    });
