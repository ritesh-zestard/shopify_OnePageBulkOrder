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

Route::get('dashboard', 'callbackController@dashboard')->name('dashboard');

Route::get('callback', 'callbackController@index')->name('callback');

Route::get('redirect', 'callbackController@redirect')->name('redirect');

Route::any('change_currency', 'callbackController@Currency')->name('change_currency');

Route::post('search', 'callbackController@validatefrontend')->middleware('cors')->name('search');

Route::any('get_variant_quantity_by_id', 'callbackController@get_variant_quantity_by_id')->middleware('cors')->name('get_variant_quantity_by_id');

Route::any('get-user-settings', 'callbackController@get_user_settings')->middleware('cors')->name('get-user-settings');

Route::get('uninstall', 'callbackController@uninstall')->name('uninstall');

Route::get('payment_process', 'callbackController@payment_method')->name('payment_process');

Route::get('payment_success', 'callbackController@payment_compelete')->name('payment_success');

Route::any('update-modal-status', 'callbackController@update_modal_status')->name('update-modal-status'); 

Route::get('help', function () {
    return view('help');
})->name('help');

Route::get('donwload-snippet', 'callbackController@download_snippet')->name('donwload-snippet');

Route::any('save', 'callbackController@save_settings')->name('save');

Route::get('link_detail', function () {
    return view('link_detail');
})->name('help');

Route::any('product_list', 'hpisumController@index')->name('product_list'); 
// Route::any('get_all_product', 'hpisumController@get_all_product')->name('get_all_product');
// Route::any('update_products', 'hpisumController@update_products')->name('update_products');

Route::post('show_variants', 'FrontendController@show_variants')->middleware('cors')->name('show_variants');
Route::get('get_all_product', 'FrontendController@get_all_product1')->middleware('cors')->name('get_all_product');

Route::get('quick_order_dashboard_save', 'SettingsController@save')->name('quick_order_dashboard_save');
Route::post('quick_order_dashboard_store', 'SettingsController@store')->name('quick_order_dashboard_store');

Route::post('update_order', 'SettingsController@update_sort_order')->name('update_order');

Route::get( '/download/{filename}', 'SettingsController@download');

Route::any('update_demo_status', 'SettingsController@update_demo_status')->name('update_demo_status');

Route::any('frontend_quick_order', 'FrontendController@frontend2')->middleware('cors')->name('frontend_quick_order');

Route::any('activate_quick_order', 'SettingsController@activate_quick_order')->name('activate_quick_order');

Route::get('quick_help', 'SettingsController@help')->name('quick_help');
