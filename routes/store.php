<?php
//CCC 20220615
Route::get('/', 'StoreController')->name('home');

Route::get('login', 'Store\LoginController')->name('login');
Route::post('login', 'Store\LoginController@login')->name('plogin');
Route::get('logout', 'Store\LoginController@logout')->name('logout');
Route::post('login/check_account', 'Store\LoginController@check_account')->name('login.check.account');
Route::get('login/find_account/{account}', 'Store\LoginController@find_account')->name('login.find.account');
Route::post('login/reset_pw', 'Store\LoginController@reset_pw')->name('login.reset.pw');
Route::get('login/find_pw/{phone}', 'Store\LoginController@find_pw')->name('login.find.pw');
Route::view('password/reset', 'store.reset_password')->name('password.reset');

Route::get('register/agree/{type}', 'Store\RegisterController@agree')->name('register.agree');
Route::post('register/get/terms', 'Store\RegisterController@get_term')->name('register.get.term');
Route::get('register/register1', 'Store\RegisterController@register1')->name('register.register1');
Route::get('register/register2', 'Store\RegisterController@register2')->name('register.register2');
Route::view('register/register3', 'store.register3')->name('register.register3');
Route::get('register/register3/{app_id}', 'Store\RegisterController@register3_byapps')->name('register.register3.appid');
Route::post('register/client_save', 'Store\RegisterController@client_save')->name('register.client.save');
Route::post('register/agent_save', 'Store\RegisterController@agent_save')->name('register.agent.save');
Route::post('register/byapps', 'Store\RegisterController@byapps')->name('register.byapps');
Route::get('register/done/{account}', 'Store\RegisterController@done')->name('register.done');

Route::get('service/detail/{service}', 'StoreController@service')->name('service.detail');
Route::get('service/detail/{service}/reviews', 'StoreController@review');
Route::get('service/detail/{service}/inquiries', 'StoreController@inquiry');
Route::get('service/detail/{service}/byapps/{app_id}', 'StoreController@service');
Route::get('search', 'StoreController@search');
Route::get('categoryinf', 'StoreController@categorylist');
Route::get('allfuntion', 'StoreController@allfuntion');
Route::get('funtioninf', 'StoreController@funtioninf');
Route::get('pick', 'StoreController@pick');
Route::get('lang/change', 'LangController@change');

Route::post('add/inquiry', 'StoreController@add_inquiry');
Route::post('add/svcreq', 'StoreController@add_servicereq');
Route::post('add/review', 'StoreController@add_review');

Route::get('satisfy', 'StoreController@satisfylist');
Route::get('matech', 'StoreController@matechlist');

Route::post('reqsvcpay', 'StoreController@reqsvcpay');
Route::post('mreqsvcpay', 'StoreController@mreqsvcpay');
Route::post('kcp_api_trade_reg', 'StoreController@kcp_api_trade_reg');
Route::post('order_mobile', 'StoreController@order_mobile');
Route::post('getorderno', 'ClientController@get_order_no');
Route::get('terms/{type}', 'StoreController@terms')->name('term');

Route::get('store/maps_content', 'StoreController@mapscontent')->name('mapscontent');
Route::get('store/maps_content_bak', 'StoreController@mapscontent_bak')->name('mapscontent_bak'); //임시 백업본
Route::post('store/maps_content_more', 'StoreController@mapscontent_more')->name('mapscontent_more');
Route::get('store/maps_content_detail/{content}', 'StoreController@mapscontentdetail')->name('mapscontent_detail');
Route::get('store/faq', 'StoreController@faq')->name('faq');
Route::post('store/faq/hits', 'StoreController@faq_hits');
Route::post('service/modal/status', 'StoreController@modal_status');


