<?php
Route::middleware('auth:api')->group(function() {

});

Route::get('oauth', 'Api\OauthController@authorizationCode');
Route::post('oauth/token', 'Api\OauthController@token');
Route::post('oauth/token/refresh', 'Api\OauthController@refresh');
Route::get('common/js', 'Api\ScriptController');
Route::get('test', 'Api\ScriptController@test');

Route::middleware('accesstoken')->group(function() {
    Route::get('client/info', 'Api\ClientController@clientInfo')->middleware('check.scope:client.read');
    Route::get('client/period', 'Api\ClientController@getPeriod')->middleware('check.scope:client.read');
    Route::put('client/period', 'Api\ClientController@updatePeriod')->middleware('check.scope:client.write');
    Route::post('client/payment', 'Api\ClientController@payment')->middleware('check.scope:client.write');
    Route::post('client/payment/one', 'Api\ClientController@payment_onetime')->middleware('check.scope:client.write');

    Route::post('script', 'Api\ScriptController@insertScript')->middleware('check.scope:script.write')->middleware('check.script');
    Route::put('script/{script_id}', 'Api\ScriptController@updateScript')->middleware('check.scope:script.write')->middleware('check.script');
    Route::delete('script/{script_id}', 'Api\ScriptController@deleteScript')->middleware('check.scope:script.write');

    Route::post('apps/notification', 'Api\AppsController@notification');
    Route::post('apps/landing', 'Api\AppsController@landing');
    Route::post('apps/retarget', 'Api\AppsController@retarget');
});
