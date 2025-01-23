<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Group prefix for UserService
Route::prefix('/user-service')->group(function () {
    Route::prefix('/users')->group(function () {
        Route::get('/{id}', function ($id) {
            // Forward request to UserService
            $response = Http::get(env('USER_SERVICE_URL'). '/api/users/{$id}');
            return $response->json();
        });

        Route::post('/login', function (Request $request) {
            // Forward request to UserService
            $response = Http::post(env('USER_SERVICE_URL'). '/api/login', $request->all());
            return $response->json();
        });
    });
});

// Group prefix for ProductService
Route::prefix('/product-service')->group(function () {
    Route::prefix('/products')->group(function () {
        Route::get('/', function (Request $request) {
            $token = $request->bearerToken();
            $response = Http::withToken($token)->get(env('PRODUCT_SERVICE_URL'). '/api/products');
            return $response->json();
        });

        Route::get('/{id}', function ($id, Request $request) {
            $token = $request->bearerToken();
            $response = Http::withToken($token)->get(env('PRODUCT_SERVICE_URL'). '/api/products/'.$id);
            return $response->json();
        });
    });
});
