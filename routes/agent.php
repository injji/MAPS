<?php
Route::get('/', 'Agent\DashBoardController')->name('dashboard');
Route::get('lang/change', 'LangController@change');
Route::get('my/info', 'AgentController@my_info')->name('my');
Route::post('my/goodbye', 'AgentController@goodbye')->name('goodbye');
Route::post('password/check', 'AgentController@check');
Route::post('my/store', 'AgentController@store')->name('my.store');
Route::get('service/create', 'Agent\ServiceController@createView')->name('service_append');
Route::get('service', 'Agent\ServiceController')->name('service');
Route::get('service/list', 'Agent\ServiceController@list')->name('service_list');
Route::post('service/create', 'Agent\ServiceController@create')->name('service_create');
Route::get('service/edit/{id}', 'Agent\ServiceController@edit')->name('service_edit');
Route::get('service/key/{api_id}', 'Agent\ServiceController@get_key')->name('get_key');
Route::post('service/store', 'Agent\ServiceController@store')->name('service_store');
Route::get('dashboard/notice', 'Agent\DashBoardController@notice')->name('notice');
Route::post('notice/hits', 'Agent\DashBoardController@notice_hits');



// // 통계
// order
Route::get('stat/order', 'Agent\StatController@order')->name('stat_order');
Route::post('stat/stat_chart', 'Agent\StatController@getStatChart')->name('stat_chart');
Route::get('stat/stat_export/{st_date}/{ed_date}/{lang}', 'Agent\StatController@stat_export')->name('stat_export');
// sales
Route::get('stat/sales', 'Agent\StatController@sales')->name('stat_sales');
// service
Route::get('stat/service', 'Agent\StatController@service')->name('stat_service');

// // 주문, 매출
Route::get('order', 'Agent\OrderController@order')->name('order.home');
Route::post('order/change_order', 'Agent\OrderController@changeOrder')->name('change_order');
Route::get('order/order_export', 'Agent\OrderController@order_export')->name('order_export');
Route::get('order/payment', 'Agent\OrderController@paymentList')->name('payment.list');
Route::get('order/refund', 'Agent\OrderController@refundList')->name('payment.refund');
Route::post('order/get_refund', 'Agent\OrderController@getRefundById')->name('get_refund');
Route::post('order/change_refund', 'Agent\OrderController@changeRefund')->name('change_refund');
Route::get('order/payment/settlement', 'Agent\OrderController@settlement')->name('payment.settlement');
Route::get('order/payment/settlement.data', 'Agent\OrderController@settlementData')->name('payment.settlement.data');
Route::get('order/payment/order_no/{order_no}', 'Agent\OrderController@matchingOrderNo')->name('payment.order_no');
Route::post('order/payment/payment', 'Agent\OrderController@createPayment')->name('payment.payment');

// 문의
Route::get('inquiry/client', 'Agent\InquiryController@client')->name('inquiry_client');
Route::post('inquiry/info', 'Agent\InquiryController@inquiry_info')->name('inquiry.info');
Route::post('inquiry/add/answer', 'Agent\InquiryController@add_answer')->name('inquiry.add.answer');
Route::get('inquiry/client/export/{st_date}/{ed_date}', 'Agent\InquiryController@client_excel_download')->name('inquiry.client.export');
Route::get('inquiry/agent', 'Agent\InquiryController@agent')->name('inquiry_agent');
Route::post('agent/inquiry/info', 'Agent\InquiryController@agent_inquiry_info')->name('agent.inquiry.info');
Route::post('add/agent/inquiry', 'Agent\InquiryController@add_agent_inquiry')->name('add.agent.inquiry');

// 리뷰
Route::get('store/review', 'Agent\ReviewController')->name('store.review');
Route::post('review/info', 'Agent\ReviewController@review_info')->name('review.info');
Route::post('add/answer', 'Agent\ReviewController@add_answer')->name('add.answer');
Route::get('review/export/{st_date}/{ed_date}', 'Agent\ReviewController@excel_download')->name('review.export');
