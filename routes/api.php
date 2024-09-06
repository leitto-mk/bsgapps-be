<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\User;
use App\Http\Controllers\Project;
use App\Http\Controllers\ProjectPic;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Catch-all route for non-existing API routes
Route::fallback(function (Request $request) {
    return response()->json([
        'status' => FALSE,
        'message' => 'The resource you are looking for does not exist.'
    ], 404);
});

Route::group(['middleware' => 'api', 'prefix' => 'v1'], function ($router) {
    Route::post('/login', [AuthController::class, 'authentication']);

    Route::group(['middleware' => ['jwt.auth', 'content.type'], 'prefix' => ''], function ($router) {
        /** USERS */
        Route::group(['prefix' => 'users'], function ($router) {
            /** GET */
            Route::get('', [User::class, 'getUsersActive'])->middleware('role.auth:,,');
            Route::get('{username}', [User::class, 'getUserByUsername'])->middleware('role.auth:1,,');

            //PATCH
            Route::patch('{idMstEmp}/admin', [User::class, 'patchIsAdmin'])->middleware('role.auth:1,,');
            Route::patch('{idMstEmp}/activate', [User::class, 'patchActivate'])->middleware('role.auth:1,,');
            Route::patch('{idMstEmp}/role', [User::class, 'patchRoleIt'])->middleware('role.auth:1,,');

            //PUT
            Route::put('{username}/sync', [User::class, 'putSync'])->middleware('role.auth:1,,');
        });


        /** PROJECTS */
        Route::group(['prefix' => 'projects'], function ($router) {
            /** GET */
            Route::get('', [Project::class, 'getProject'])->middleware('role.auth:,,');
            Route::get('{idProject}/detail', [Project::class, 'getProjectDetail'])->middleware('role.auth:,,');
            Route::get('summary', [Project::class, 'getProjectSummary'])->middleware('role.auth:,,');
            Route::get('progress', [Project::class, 'getProjectProgress'])->middleware('role.auth:,,');
            Route::get('released', [Project::class, 'getProjectReleased'])->middleware('role.auth:,,');

            /** POST */
            Route::post('', [Project::class, 'postProject'])->middleware('role.auth:,,');
        });

        /** PROJECT PIC */
        Route::group(['prefix' => 'project-pic'], function ($router) {
            /** POST */
            Route::post('', [ProjectPic::class, 'postProjectPic'])->middleware('role.auth:,1,');
            Route::delete('{id_trx_project_pic}', [ProjectPic::class, 'deleteProjectPic'])->middleware('role.auth:,1,');
        });
    });
});
