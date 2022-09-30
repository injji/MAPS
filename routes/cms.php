<?php
Route::get('login', 'Auth\CmsLoginController');
Route::get('oauth/innofam/redirect', 'Auth\CmsLoginController@innofamLogin');
Route::post('login', 'Auth\CmsLoginController@login');
Route::get('logout', 'Auth\CmsLoginController@logout');

Route::middleware(['auth:cms'])->group(function () {
    Route::get('/', 'Cms\DashBoardController@cmsView')->name('home');
    // 서비스관리
    Route::get('service/list', 'Cms\ServiceController@list')->name('service.list');
    Route::get('service/export/{st_date}/{ed_date}/{agent_id}/{service_id}/{category_id}', 'Cms\ServiceController@service_excel_download')->name('service.export');
    Route::get('service/edit/{id}', 'Cms\ServiceController@edit')->name('service_edit');
    Route::post('update/service/process', 'Cms\ServiceController@update_service_process')->name('update.service.process');
    Route::get('service/key/{api_id}', 'Cms\ServiceController@get_key')->name('get_key');
    Route::post('service/store', 'Cms\ServiceController@store')->name('service_store');
    Route::get('service/evaluate', 'Cms\ServiceController@evaluate')->name('service.evaluate');
    Route::get('service/restoration', 'Cms\ServiceController@restoration')->name('service.restoration');
    Route::get('service/display', 'Cms\ServiceController@service_display')->name('service.service_display');
    Route::post('service/sort_display', 'Cms\ServiceController@sort_display')->name('service.service_display.sort');


    // 업체관리
    Route::get('client/list', 'Cms\CompanyController@client_list')->name('company.client');
    Route::get('company/export', 'Cms\CompanyController@excel_download')->name('company.export');
    Route::get('client/edit/{id}', 'Cms\CompanyController@client_edit')->name('client_edit');
    Route::post('user/info/store', 'Cms\CompanyController@user_info_store');
    Route::get('user/admin/{type}/{id}', 'Auth\UserLoginController@login_from_cms')->name('user.admin');
    Route::get('agent/list', 'Cms\CompanyController@agent_list')->name('company.agent');
    Route::get('agent/edit/{id}', 'Cms\CompanyController@agent_edit')->name('agent_edit');
    Route::get('agent/list', 'Cms\CompanyController@agent_list')->name('company.agent');
    Route::get('agent/list', 'Cms\CompanyController@agent_list')->name('company.agent');
    Route::get('inquiry/client', 'Cms\CompanyController@inquiry_client')->name('company.client_question');
    Route::post('client/inquiry/info', 'Cms\CompanyController@client_inquiry_info');
    Route::post('client/inquiry/add/answer', 'Cms\CompanyController@client_inquiry_add_answer');
    Route::post('inquiry/client_info', 'Cms\CompanyController@inquiry_client_info')->name('inquiry.client_info');
    Route::get('inquiry/agent', 'Cms\CompanyController@inquiry_agent')->name('company.agent_question');
    Route::post('agent/inquiry/info', 'Cms\CompanyController@agent_inquiry_info');
    Route::post('agent/inquiry/add/answer', 'Cms\CompanyController@agent_inquiry_add_answer');
    Route::post('inquiry/agent_info', 'Cms\CompanyController@inquiry_agent_info')->name('inquiry.agent_info');
    Route::get('review', 'Cms\CompanyController@review')->name('company.review');
    Route::post('update/review/visible', 'Cms\CompanyController@update_review_visible')->name('update.review.visible');
    Route::post('del/review', 'Cms\CompanyController@del_review')->name('del.review');
    Route::post('review/info', 'Cms\CompanyController@review_info');
    Route::post('client/site/count', 'Cms\CompanyController@site_count');
    Route::get('goodbye', 'Cms\CompanyController@goodbye')->name('company.goodbye');
    Route::get('goodbye/export', 'Cms\CompanyController@goodbye_export')->name('company.goodbye_export');
    Route::post('company/goodbye_status', 'Cms\CompanyController@goodbye_status')->name('company.goodbye.status');

    // 카테고리관리
    Route::get('category', 'Cms\CategoryController@list')->name('category.service');
    Route::post('category/update_category', 'Cms\CategoryController@changeCategory');
    // 주문, 매출
    Route::get('order', 'Cms\OrderController@order')->name('order.list');
    Route::post('order/change_order', 'Cms\OrderController@changeOrder')->name('change_order');
    Route::get('order/order_export', 'Cms\OrderController@order_export')->name('cms_order_export');
    Route::get('order/payment', 'Cms\OrderController@paymentList')->name('order.payment');
    Route::get('order/refund', 'Cms\OrderController@refundList')->name('order.refund');
    Route::post('order/get_refund', 'Cms\OrderController@getRefundById')->name('get_refund');
    Route::post('order/change_refund', 'Cms\OrderController@changeRefund')->name('change_refund');
    Route::post('order/change_sattle', 'Cms\OrderController@changeSattle')->name('change_sattle');
    Route::get('order/settle_summary', 'Cms\OrderController@settle_summary')->name('order.settle_summary');
    Route::get('order/settle_detail', 'Cms\OrderController@settle_detail')->name('order.settle_detail');
    Route::get('order/chnage_refund_alarm', 'Cms\OrderController@changeRefundAlarm');

    // 통계
    Route::get('stat/using', 'Cms\StatController@using')->name('stat.using');
    Route::post('stat/stat_chart', 'Cms\StatController@getStatChart')->name('stat_chart');
    Route::get('stat/stat_export', 'Cms\StatController@stat_export')->name('stat_export');
    Route::get('stat/service', 'Cms\StatController@service')->name('stat.service');
    Route::get('stat/category', 'Cms\StatController@category')->name('stat.category');
    Route::get('stat/agent', 'Cms\StatController@agent')->name('stat.agent');

    // 스토어관리
    Route::get('cms_store/banner', 'Cms\StoreController@banner')->name('store.banner');
    Route::post('cms_store/get_banner', 'Cms\StoreController@getBannerById');
    Route::post('cms_store/change_banner', 'Cms\StoreController@changeBanner');
    Route::get('cms_store/func', 'Cms\StoreController@func')->name('store.func');
    Route::get('cms_store/func_register', 'Cms\StoreController@func_register')->name('store.func_register');
    Route::get('cms_store/func_detail', 'Cms\StoreController@func_detail')->name('store.func_detail');
    Route::post('cms_store/change_func', 'Cms\StoreController@changeFuncKind')->name('change_func');
    Route::post('cms_store/get_service', 'Cms\StoreController@getService');
    Route::get('cms_store/conte', 'Cms\StoreController@conte')->name('store.conte');
    Route::get('cms_store/conte_register', 'Cms\StoreController@conte_register')->name('store.conte_register');
    Route::post('cms_store/preview', 'Cms\StoreController@conte_preview')->name('store.conte_preview');
    Route::get('cms_store/maps_content_preview', 'Cms\StoreController@mapscontentpreview')->name('store.preview');
    Route::post('cms_store/conte_store', 'Cms\StoreController@conte_store')->name('store.conte_store');
    Route::post('cms_store/conte_delete', 'Cms\StoreController@conte_delete')->name('store.conte_delete');
    Route::post('cms_store/conte_sort', 'Cms\StoreController@conte_sort')->name('store.conte_sort');

    // 환경설정
    Route::get('setting/admin', 'Cms\SettingController@admin')->name('setting.admin');
    Route::post('setting/get/menu', 'Cms\SettingController@get_menu')->name('setting.get.menu');
    Route::post('setting/update/menu', 'Cms\SettingController@update_menu')->name('setting.update.menu');
    Route::post('setting/update/admin/use', 'Cms\SettingController@update_admin_use')->name('setting.update.admin.use');
    Route::post('setting/get/menu/permission/list', 'Cms\SettingController@get_permission_list');
    Route::post('setting/save/menu/permission', 'Cms\SettingController@save_permission');
    Route::get('setting/question', 'Cms\SettingController@question')->name('setting.question');
    Route::get('setting/faq_set', 'Cms\SettingController@faq_set')->name('setting.faq_set');
    Route::get('setting/notice', 'Cms\SettingController@notice')->name('setting.notice');
    Route::get('setting/notice_register', 'Cms\SettingController@notice_register')->name('setting.notice_register');
    Route::get('setting/notice_detail', 'Cms\SettingController@notice_detail')->name('setting.notice_detail');
    Route::post('setting/notice_update', 'Cms\SettingController@changeNotice');
    Route::post('setting/notice_delete', 'Cms\SettingController@deleteNotice');
    Route::get('setting/site', 'Cms\SettingController@site')->name('setting.site');
    Route::post('setting/site_update', 'Cms\SettingController@changeSiteInfo');
    Route::get('setting/term', 'Cms\SettingController@term')->name('setting.term');
    Route::post('setting/term_update', 'Cms\SettingController@changeTerm');
    Route::post('setting/faq', 'Cms\SettingController@faq');
    Route::post('setting/faq_update', 'Cms\SettingController@faqUpdate');
    Route::post('setting/faq_delete', 'Cms\SettingController@faqDelete');
    Route::post('setting/faq_order', 'Cms\SettingController@faqOrderUpdate');
    Route::get('setting/script_set', 'Cms\SettingController@script_set')->name('setting.script_set');
    Route::post('setting/script_request', 'Cms\SettingController@script_request')->name('setting.script_request');
});
