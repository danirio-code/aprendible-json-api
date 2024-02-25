<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Middleware\ValidateJsonApiDocument;

Route::apiResource('articles', ArticleController::class)->names('api.v1.articles');

Route::withoutMiddleware(ValidateJsonApiDocument::class)
  ->post('login', [LoginController::class, '__invoke'])
  ->name('api.v1.login');
