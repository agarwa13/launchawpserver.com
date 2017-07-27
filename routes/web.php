<?php

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

/*
 * Home Page
 */
Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', 'HomeController@index')->name('home');

/*
 * The Guides
 */
Route::get('credentials/guide','CredentialController@guide');
Route::get('servers/guide','ServerController@guide');

/*
 * Personalized Launch
 */
Route::get('hand-held-launch','HomeController@handHeldLaunch');

//Route::get('test-launcher','LaunchController@testLauncher');

/*
 * Notification URL for Server Upgraded
 */
Route::get('servers/{id}/server-upgraded','ServerController@serverUpgraded');

Route::resource('launch','LaunchController');
Route::resource('sites','SiteController');
Route::resource('servers','ServerController');
Route::resource('credentials','CredentialController');

Auth::routes();


