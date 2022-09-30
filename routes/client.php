<?php
Route::get('/', 'ClientController')->name('dashboard');
Route::get('/{site_id}', 'ClientController');
Route::get('my/info', 'ClientController@my_info')->name('my');
Route::post('my/goodbye', 'ClientController@goodbye')->name('goodbye');
Route::post('password/check', 'ClientController@check');
Route::post('my/store', 'ClientController@store')->name('my.store');
Route::get('my/service', 'ClientController@myservice')->name('myservice');
Route::get('my/service/{kind}', 'ClientController@myservice_search')->name('myservice.search');
Route::post('update/service/process', 'ClientController@update_service_process')->name('update.service.process');
Route::post('get/service/review', 'ClientController@get_service_review')->name('get.service.review');
Route::post('add/service/review', 'ClientController@add_service_review')->name('add.service.review');
Route::post('add/service/inquiry', 'ClientController@add_service_inquiry')->name('add.service.inquiry');
Route::post('service/refund', 'ClientController@refund_service')->name('service.refund');
Route::post('get/service/info', 'ClientController@get_service_info');
Route::get('site/set', 'ClientController@site_list')->name('site.set');
Route::post('site/create', 'ClientController@create_site')->name('site.create');
Route::post('site/delete', 'ClientController@delete_site')->name('site.delete');
Route::post('site/store', 'ClientController@store_site')->name('site.store');
Route::get('payment/list', 'ClientController@payment_list')->name('payment_list');
Route::get('payment/refund', 'ClientController@refund')->name('refund');
Route::post('payment/info', 'ClientController@payment_info')->name('payment.info');
Route::post('update/refund/reason', 'ClientController@update_refund_reason')->name('update.refund.reason');
Route::post('update/refusal/reason', 'ClientController@update_refusal_reason')->name('update.refusal.reason');
Route::get('bbs/inquiry', 'ClientController@inquiry')->name('inquiry');
Route::post('inquiry/info', 'ClientController@inquiry_info')->name('inquiry.info');
Route::get('bbs/review', 'ClientController@review')->name('review');
Route::post('review/info', 'ClientController@review_info')->name('review.info');
Route::get('dashboard/notice', 'ClientController@notice')->name('notice');
Route::post('notice/hits', 'ClientController@notice_hits');
Route::post('reqextendpay', 'ClientController@reqextendpay');
Route::post('get/orderno', 'ClientController@get_order_no');
Route::get('script/guide', 'ClientController@guide')->name('guide');
Route::post('script/request', 'ClientController@script_request')->name('script.request');


Route::get('lang/change', 'LangController@change');
Route::view('partials/create/site', 'partials.client.site.create');
Route::view('partials/edit/site', 'partials.client.site.edit');
Route::view('partials/create/select', 'partials.client.site.select');

