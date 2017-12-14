<?php

/**
 * Frontend Controllers
 * All route names are prefixed with 'frontend.'.
 */

use Illuminate\Support\Facades\Route;

Route::get('/', 'FrontendController@index')->name('index');
Route::get('macros', 'FrontendController@macros')->name('macros');
Route::get('contact', 'ContactController@index')->name('contact');
Route::post('contact/send', 'ContactController@send')->name('contact.send');

//CRUD resources
Route::group([ 'namespace' => 'Project' ],
    function() {
        CRUD::resource('projects', 'ProjectCrudController');
        CRUD::resource('releases', 'ProjectReleaseCrudController');
        //Route::get('projects/{project}', 'ProjectController@show')->name('project.show');
    });
Route::group([ 'namespace' => 'Project', 'prefix' => 'projects/{project_id}' ],
    function() {
        CRUD::resource('releases', 'ProjectReleaseCrudController');
    });

/* ----------------------------------------------------------------------- */
Route::get('projects/{project}/imports/create', 'ImportCrudController@importProject')->name('project.import.create');
Route::post('projects/{project}/imports/create', 'ImportCrudController@createBatch')->name('project.import.create.post');
Route::get('projects/{project}/imports/{batch}/{step}', 'ImportCrudController@importProject')->name('project.import');
Route::post('projects/{project}/imports/{batch}/{step}', 'ImportCrudController@processImportProject')->name('project.import.post');
/*
 * These frontend controllers require the user to be logged in
 * All route names are prefixed with 'frontend.'
 */
Route::group([ 'middleware' => 'auth' ],
    function() {
        Route::group([ 'namespace' => 'User', 'as' => 'user.' ],
            function() {
                /*
                 * User Dashboard Specific
                 */
                Route::get('dashboard', 'DashboardController@index')->name('dashboard');

                /*
                 * User Account Specific
                 */
                Route::get('account', 'AccountController@index')->name('account');

                /*
                 * User Profile Specific
                 */
                Route::patch('profile/update', 'ProfileController@update')->name('profile.update');
            });
    });

