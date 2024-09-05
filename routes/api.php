<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UploadController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/api/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

Route::post('upload-document', [UploadController::class, 'store'])->name('api.upload-document');
Route::post('extract-content', [UploadController::class, 'extractContent'])->name('api.extract-content');
Route::get('get-documents', [UploadController::class, 'getDocuments'])->name('api.get-documents');
