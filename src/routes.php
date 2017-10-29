<?php

Route::group([
    'prefix'     => 'admin/logs',
    'middleware' => ['admin', 'auth.admin'],
    'namespace'  => 'LaraMod\Admin\Logs\Controllers',
], function () {
    Route::get('/', ['as' => 'admin.logs', 'uses' => 'LogsController@index']);
    Route::get('form', ['as' => 'admin.logs.form', 'uses' => 'LogsController@getForm']);
    Route::get('/delete', ['as' => 'admin.logs.delete', 'uses' => 'LogsController@delete']);
    Route::get('/delete/file', ['as' => 'admin.logs.delete.file', 'uses' => 'LogsController@deleteFile']);
});
