<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
});

Route::get("/login", function () {
    return response()->make(
        "This application requires a token to be passed before accessing resources !",
        Response::HTTP_UNAUTHORIZED
    );
})->name("login");
