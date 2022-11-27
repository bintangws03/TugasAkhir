<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'ShowLoginForm']);

Auth::routes(['register' => false]);

Route::prefix('admin')->group(function () {
    Route::group(['middleware' => 'auth'], function(){
    //dashboard
    Route::get('/dashboard',[App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard.index');
    //permissions
    Route::resource('/permission',App\Http\Controllers\Admin\PermissionController::class, ['except' => ['show','create', 'edit', 'update', 'delete'] ,'as' => 'admin']);
    //roles
    Route::resource('/role', App\Http\Controllers\Admin\RoleController::class,['except' => ['show'] ,'as' => 'admin']);
    });
    //users
    Route::resource('/user', App\Http\Controllers\Admin\UserController::class, ['except'=> ['show'] ,'as' => 'admin']);
    //tags
    Route::resource('/tag', App\Http\Controllers\Admin\TagController::class, ['except' =>'show' ,'as' => 'admin']);
    //categories
    Route::resource('/category', App\Http\Controllers\Admin\CategoryController::class,['except' => 'show' ,'as' => 'admin']);
    //posts
    Route::resource('/post', App\Http\Controllers\Admin\PostController::class, ['except'=> 'show' ,'as' => 'admin']);
    //photo
    Route::resource('/photo', App\Http\Controllers\Admin\PhotoController::class,['except' => ['show', 'create', 'edit', 'update'] ,'as' => 'admin']);
    // jurusan
    Route::resource('/jurusan', App\Http\Controllers\Admin\JurusanController::class, ['except' => 'show' ,'as' => 'admin']);
    // prestasi
    Route::resource('/prestasi', App\Http\Controllers\Admin\PrestasiController::class, ['except' => 'show' ,'as' => 'admin']);
    //produksi
    Route::resource('/produksi', App\Http\Controllers\Admin\ProduksiController::class, ['except'=> 'show' ,'as' => 'admin']);



});

