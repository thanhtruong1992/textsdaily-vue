<?php
/*
 * |--------------------------------------------------------------------------
 * | Web Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register web routes for your application. These
 * | routes are loaded by the RouteServiceProvider within a group which
 * | contains the "web" middleware group. Now create something great!
 * |
 */
Route::group([
    'prefix' => '/admin'
], function() {
    require_once "admin.php";
    return;
});

Route::get('/{any}', function(){
    return view('index');
})->where('any', '.*');
// Route::get('/short-link', function () {
//     return view('short-link');
// });
// Route::get ( '/test', 'TestController@index' );
// Route::get ( '/info', function() {
//     echo phpinfo();
// });
// Route::get ( '/get-data-request', 'TestController@getDataTest');
// Route::get ( '/report-tm-sms', 'ReportController@createTMSMSReport' );
// Route::post ( '/report-infobip-sms', 'ReportController@createInfoBipSMSReport' );
// Route::post ( '/report-messagebird-sms', 'ReportController@createMessageBirdSMSReport' );
// Route::post ( '/report-route-mobile', 'ReportController@createRouteMobileReport' );
// Route::get ( '/404', function () { return view('errors.404'); })->name('error.404');
// Route::get ( '/403', function () { return view('errors.403'); })->name('error.403');
// Route::get ( '/500', function () { return view('errors.500'); })->name('error.500');
// Route::get ( '/503', function () { return view('errors.503'); })->name('error.503');
// Route::get ( '/cron-send-campaign', 'CampaignController@cronSendCampaign' )->name ( 'campaign.send' );
// Route::get ( '/cron-report-status', 'CampaignController@cronGetDeliveryReports' )->name ( 'campaign.deliveryReports' );
// Route::get ( '/cron-move-data-report', "ReportController@moveDataQueueToReportListSummary" );
// // Route::get ( '/cron-detect-subscribers', "SubscriberController@cronDetectSubscribers" );
// // Route::get ( '/transfer-credits-monthly', 'ClientController@transferCreditsMonthly' )->name ( 'client.transfer' );
// // Route::get ( '/cron-report-center', "ReportController@cronJobReportCenter" );
// // Route::get ( '/cron-auto-report', "ReportController@cronAutoReport" );
// // Route::get ( '/cron-send-email-report', "ReportController@cronSendEmailReport" );
// Route::get ( '/cron-get-received-messages', "CampaignController@cronGetReceivedMessages" )->name('cronGetReceivedMessages');
// // Route::get ( '/cron-send-campaign-paused', "CampaignController@sendAgainCampaugnPaused" );

// Route::group([
//         "prefix" => "/",
//         "middleware" => ["language", "domain"]
// ], function() {
//     // Ajax
//     Route::group([
//             "prefix" => "/admin/apis",
//             "middleware" => "authentication"
//     ], function() {
//         Route::get ( '/subscribers/{id}', "SubscriberController@getListSubscribers" );
//         Route::get ( '/subscribers/{id}/export', "SubscriberController@exportSubscribers" );
//         Route::delete ( '/subscribers/{id}', "SubscriberController@destroy" );
//         Route::get ( '/subscribers-list', "SubscriberListController@index" );
//         Route::get ( '/subscribers-list/{id}', "SubscriberListController@summarySubscribers" );
//         Route::post ( '/subscribers-list/delete/', "SubscriberListController@delete" );
//         Route::get ( '/campaigns', 'CampaignController@getCampaignByQuery' );
//         Route::post ( '/campaigns/delete', 'CampaignController@delete' );
//         Route::post ( '/campaigns/short-link', "CampaignController@shortLink" );
//         Route::get ( '/campaigns/pesonalize', "CustomFieldController@getCustomField" )->name ( 'customfield.personalize' );
//         Route::get ( '/campaigns/templates', "TemplateController@getTemplateByQuery" )->name ( 'template.template' );
//         Route::post ( '/campaigns/amend', 'CampaignController@amend' );
//         Route::post ( '/campaigns/update-status', 'CampaignController@updateStatusCampaign' );
//         Route::post ( '/custom-fields/add', "CustomFieldController@create" );
//         Route::post ( '/campaigns/test-send-message', "CampaignController@testSendSMS" )->name ( 'campaign.test_sms' );
//         Route::post ( '/campaign/add-sender', "CampaignController@addSender" )->name ( 'campaign.add_sender' );
//         Route::get ( "/campaigns/summary", "CampaignController@summaryCampaign" );
//         Route::post ( '/campaigns/clone', 'CampaignController@cloneCampaign' );
//         Route::get ( '/campaigns/total-send', 'CampaignController@totalSend' );
//         Route::get ( '/templates', 'TemplateController@getTemplateByQuery' )->name('templates.get_template');
//         Route::post ( '/templates/delete', 'TemplateController@delete' )->name('templates.delete');
//         Route::get ( '/reports/campaigns', 'ReportController@listReportCampaign');
//         Route::get ( '/reports/campaigns/{user_id}/{campaign_id}', 'ReportController@reportCampaign');
//         Route::get ('/reports/campaigns/{id}/export-csv', 'ReportController@exportCSVCampaign');
//         Route::get ('/reports/campaigns/{id}/export-pdf', 'ReportController@exportPDFCampaign');
//         Route::post ( '/ajax-get-price-configuration-by-country-network', "ClientController@ajaxGetPriceConfiguration" )->name('clients.ajaxGetPriceConfiguration');
//         Route::post ( '/ajax-save-price-configuration', "ClientController@ajaxSavePriceConfiguration" )->name('clients.ajaxSavePriceConfiguration');
//         Route::get ('/reports/center', 'ReportController@reportCenter')->name("report-center.api");
//         Route::get ('/report-center', 'ReportController@getDataReportCenter')->name("report-center.data");
//         Route::get ('/download-report-center/{hash}', 'ReportController@downloadFileCsvReportCenter')->name("download-report-center");
//         Route::post ( '/ajax-get-mcc-mnc', "SettingsController@ajaxGetMCCMNC" )->name('setting.ajaxGetMCCMNC');
//         Route::post ( '/ajax-get-mobile-pattern', "SettingsController@ajaxGetMobilePattern" )->name('ajaxGetMobilePattern');
//         Route::post ( '/ajax-get-inbound-config', "SettingsController@ajaxGetInboundConfig" )->name('ajaxGetInboundConfig');
//         Route::post ( '/inbound-config/{id}', "SettingsController@updateInboundConfig" )->name('updateInboundConfig');
//         Route::post ( '/unassign-inbound-config', "SettingsController@unassignInboundConfig" )->name('unassignInboundConfig');
//         Route::get ( '/ajax-get-inbound-messages', "SettingsController@ajaxGetInboundMessages" )->name('ajaxGetInboundMessages');
//         Route::post ( '/check_username', 'ClientController@checkUsername' )->name('ajaxCheckUsername');
//         Route::get ('/loginWithOtherrole/{id}', "Auth\LoginController@loginWithOtherRole" )->name('loginOther.other');
//         Route::get ('/returnParent', "Auth\LoginController@loginWithParentRole" )->name('loginOther.return');
//         Route::get("/price-config-sample", "ClientController@downloadTemplacePriceConfig")->name('client.downloadTemplacePriceConfig');
//         Route::post ( '/ajax-upload-price-config/{id}', "ClientController@ajaxUploadPriceConfiguration" )->name('ajaxUploadPriceConfiguration');
//         Route::get ( '/export-inbound-message', "SettingsController@exportInboundMessage" )->name('inbound-message.export');
//         Route::get ('/add-unsubscriber/{campaign_id}', "CampaignController@addUnsubscriber")->name('campaign.unsubscriber');
//     });

//     // Ajax group 4
//     Route::group([
//             "prefix" => "/reader/apis",
//             "middleware" => "authentication"
//     ], function() {
//         Route::get ( '/subscribers-list', "SubscriberListController@index" );
//         Route::get ( '/subscribers-list/{id}', "SubscriberListController@summarySubscribers" );
//         Route::get ( '/subscribers/{id}/export', "SubscriberController@exportSubscribers" );
//         Route::get ( '/campaigns', 'CampaignController@getCampaignByQuery' );
//     });

//     //
//     Route::get ( '/', "Auth\LoginController@viewLogin" );
//     // Login
//     Route::prefix ( 'login' )->group ( function () {
//         Route::get ( '/', "Auth\LoginController@viewLogin" );
//         Route::post ( '/', "Auth\LoginController@login" );
//     } );
//     // Register
//     Route::prefix ( 'register' )->group ( function () {
//         Route::get ( '/', function () {
//             return view ( 'register' );
//         } );
//             Route::post ( '/', 'Auth\RegisterController@create' );
//     } );
//     // Forgot password
//     Route::prefix('/forgot-password')->group(function() {
//         Route::get ('/', function() {
//             return view("forgot-password");
//         });
//         Route::post('/', "Auth\ForgotPasswordController@forgotPassword")->name("forgot-passowrd");
//     });
//     // Reset Password
//     Route::prefix('/reset-password')->group(function() {
//         Route::get('/', 'Auth\ResetPasswordController@viewResetPassowrd');
//         Route::post('/', 'Auth\ResetPasswordController@resetPassword')->name('reset-password');
//     });
//     // Redirect short link
//     Route::get ( "redirect-link", "CampaignController@redirectShorLLink" );
//     // template pdf
//     Route::get ("/template-pdf", "ReportController@templatePDF");

//     // unsubscribe
//     Route::get ( '/unsubscribe', "SubscriberController@viewUnsubscribe" );
//     Route::post ( '/unsubscribe', "SubscriberController@unsubscribe" );

//     // Group 1
//     Route::group ( [
//             'prefix' => 'agency',
//             'middleware' => [
//                     'authentication',
//                     'group1'
//             ]
//     ], function () {

//         Route::get ( '/dashboard', "DashBoardController@index" );
//         Route::get ( '/logout', "Auth\LoginController@logout" );
//         // setting
//         Route::group ( [
//                 'prefix' => '/settings'
//         ], function () {
//             Route::get("/", function() {
//                 return redirect ()->route('setting.account');
//             })->name('setting.index');
//             Route::get ( '/account', "SettingsController@settingAccount" )->name('setting.account');
//             // Mccmnc
//             Route::get ( '/mnc-mcc', "SettingsController@settingMCCMNC" )->name('settings.mnc-mcc');
//             Route::post ( '/mnc-mcc/delete', 'SettingsController@deleteMCCMNC')->name( 'settings.mnc-mcc.delete' );
//             Route::get ( '/mnc-mcc/edit/{id}', 'SettingsController@editMCCMNC')->name( 'settings.mnc-mcc.edit' );
//             Route::post ( '/mnc-mcc/update/{id}', 'SettingsController@updateMCCMNC')->name( 'settings.mnc-mcc.update' );
//             Route::post ( '/ajax-upload-mcc-mnc', "SettingsController@ajaxUploadMCCMNC" )->name('ajaxUploadMCCMNC');
//             //
//             Route::get ( '/whitelabel', "SettingsController@settingWhiteLabel" )->name('setting.whitelabel');
//             Route::post ( '/account-update', "SettingsController@updateAccount" )->name('setting.account-update');
//             Route::post ( '/whitelabel-update', "SettingsController@updateWhiteLabel" )->name('setting.whitelabel-update');
//             Route::get ( '/service-provider', "SettingsController@serviceProvider" )->name('serviceProviderIndex');
//             Route::post ( '/ajax-get-service-provider-by-country-network', "SettingsController@ajaxGetPreferredServiceProvider" )->name('ajaxGetPreferredServiceProvider');
//             Route::post ( '/ajax-save-service-provider', "SettingsController@ajaxSaveServiceProvider" )->name('ajaxSaveServiceProvider');
//             Route::post ( '/ajax-upload-service-provider', "SettingsController@ajaxUploadServiceProvider" )->name('ajaxUploadServiceProvider');
//             // Mobile pattern
//             Route::get ( '/mobile-pattern', "SettingsController@settingMobilePattern" )->name('settingMobilePattern');
//             Route::post ( '/mobile-pattern/delete', 'SettingsController@deleteMobilePattern')->name( 'deleteMobilePattern' );
//             Route::get ( '/mobile-pattern/edit/{id}', 'SettingsController@editMobilePattern')->name( 'editMobilePattern' );
//             Route::post ( '/mobile-pattern/update/{id}', 'SettingsController@updateMobilePattern')->name( 'updateMobilePattern' );
//             Route::post ( '/ajax-upload-mobile-pattern', "SettingsController@ajaxUploadMobilePattern" )->name('ajaxUploadMobilePattern');
//             // Report setting
//             Route::get ( '/report', "SettingsController@settingReport" )->name('settings.report');
//             Route::post ( '/report-update', "SettingsController@settingReportUpdate" )->name('settings.report-update');
//             // Inbound config
//             Route::get ( '/inbound-config', "SettingsController@settingInboundConfig" )->name('inboundConfig');
//             Route::get ( '/inbound-config/{id}', "SettingsController@editInboundConfig" )->name('editInboundConfig');
//         } );

//         // Clients
//         Route::group ( [
//                 'prefix' => 'clients'
//         ], function () {
//             Route::get ( '/', "ClientController@index")->name ( 'clients.index' );
//             Route::group([
//                     'prefix' => 'create'
//             ], function() {
//                 Route::get ( '/account', "ClientController@create")->name ( 'clients.create' );
//                 Route::get ( '/billing', "ClientController@updateBilling")->name ( 'clients.billing' );
//                 Route::get ( '/account/{id}', "ClientController@info")->name ( 'clients.info' );
//                 Route::get ( '/billing/{id}', "ClientController@infoBilling")->name ( 'clients.info-billing' );
//                 Route::post ( '/account', "ClientController@store")->name ( 'clients.store' );
//                 Route::post ( '/account/{id}', "ClientController@update")->name ( 'clients.update' );

//                 // Ajax
//                 Route::group ( [
//                         "prefix" => "/apis"
//                 ], function () {
//                     Route::post ( '/account/delete', 'ClientController@delete')->name ( 'clients.delete' );
//                     Route::post ( '/account/updateStatus', 'ClientController@updateStatusClient')->name ( 'clients.updateStatusClient' );
//                     Route::post ( '/account/addCredit', 'ClientController@addCredit')->name ( 'clients.addCredit' );
//                     Route::post ( '/account/increaseCredit', 'ClientController@increaseCredit')->name ( 'clients.increaseCredit' );
//                     Route::post ( '/account/descreaseCredit', 'ClientController@descreaseCredit')->name ( 'clients.descreaseCredit' );
//                     Route::post ( '/account/withdrawCredit', 'ClientController@withdrawCredit')->name ( 'clients.withdrawCredit' );
//                 } );
//             });
//             Route::get ( '/{id}/price-config', "ClientController@priceConfig")->name ( 'clients.priceConfig' );
//         } );

//         // report
//         Route::group([
//                 "prefix" => "/reports"
//         ], function() {
//             Route::get("/campaigns", function() {
//                 return view("admins.reports.campaigns.index");
//             })->name('report-campaign-1.index');
//             Route::get("/campaigns/{user_id}/{campaign_id}", 'ReportController@viewDetailReportCampaign')->name("report-campaign-1.detail");
//             Route::get ('/campaigns/{user_id}/{campaign_id}/export-csv', 'ReportController@exportCSVCampaign')->name("export-campaign-1.csv");
//             Route::get ('/campaigns/{user_id}/{campaign_id}/export-pdf', 'ReportController@exportPDFCampaign')->name("export-campaign-1.pdf");
//             Route::get('/center', "ReportController@viewReportCenter")->name("report-center-1.index");
//         });

//         // transaction history
//         Route::group([
//                 "prefix" => "/transaction-histories"
//         ], function() {
//             Route::get('/client', 'TransactionHistoryController@viewClient')->name("transaction-histories.client.index");
//             Route::get('/client/csv', 'TransactionHistoryController@exportClient')->name("transaction-histories.client.csv");
//             Route::get("/service-provider", 'TransactionHistoryController@viewServiceProvider')->name("transaction-histories.campaigns.index");
//             Route::get("/service-provider/csv", 'TransactionHistoryController@exportCampaignsCSV')->name("transaction-histories.campaigns.csv");
//             Route::get("/service-provider/{user_id}/{campaign_id}", 'TransactionHistoryController@getCampaign')->name("transaction-histories.campaigns.detail");
//             Route::get("/service-provider/{user_id}/{campaign_id}/csv", 'TransactionHistoryController@exportCSVReportCampaign')->name("transaction-histories.campaigns.detail.csv");

//             // api
//             Route::group([
//                     "prefix" => "/apis"
//             ], function() {
//                 Route::get("/client", "TransactionHistoryController@getDataClient")->name('transaction-histories.clients');
//                 Route::get("/campaign", "TransactionHistoryController@getDataCampaigns")->name("api.transaction-histories.getCampaigns");
//                 Route::get("/servie_provider_sample", "SettingsController@getSampleServieProvider")->name('settings.get_service_provider');
//                 Route::get("/mcc_mnc_sample", "SettingsController@getSampleMCCMNC")->name('settings.get_mcc_mnc');
//                 Route::get("/mobile-pattern-sample", "SettingsController@getSampleMobilePattern")->name('settings.get_mobile_pattern_sample');
//             });
//         });
//     } );

//     // Group 2
//     Route::group ( [
//             'prefix' => 'client',
//             'middleware' => [
//                     'authentication',
//                     'group2'
//             ]
//     ], function () {
//         //
//         Route::get ( '/dashboard', "DashBoardController@index" );
//         Route::get ( '/logo', "ClientController@responseLogo");
//         Route::get ( '/logout', "Auth\LoginController@logout" );

//         // Setting
//         Route::group ( [
//                 'prefix' => '/settings'
//         ], function () {
//             Route::get("/", function() {
//                 return redirect ()->route('setting2.account');
//             })->name('setting2.index');
//             Route::get ( '/account', "SettingsController@settingAccount" )->name('setting2.account');
//             Route::get ( '/whitelabel', "SettingsController@settingWhiteLabel" )->name('setting2.whitelabel');
//             Route::post ( '/account-update', "SettingsController@updateAccountGroup2" )->name('setting2.account-update');
//             Route::post ( '/whitelabel-update', "SettingsController@updateWhiteLabel" )->name('setting2.whitelabel-update');
//             // Inbound config
//             Route::get ( '/inbound-config', "SettingsController@settingInboundConfig" )->name('inboundConfigClient');
//             Route::get ( '/inbound-config/{id}', "SettingsController@editInboundConfig" )->name('editInboundConfigClient');
//         } );

//         // Clients
//         Route::group ( [
//                 'prefix' => 'clients'
//         ], function () {
//             Route::get ( '/', "ClientController@index")->name ( 'clients2.index' );
//             Route::group([
//                     'prefix' => 'create'
//             ], function() {
//                 Route::get ( '/account', "ClientController@create")->name ( 'clients2.create' );
//                 Route::get ( '/account/reader', "ClientController@createReader")->name ( 'clients2.createReader' );
//                 Route::get ( '/account/api', "ClientController@createApiAccount")->name ('clients2.createApiAccount');
//                 Route::get ( '/account/reader/{id}', "ClientController@infoReader")->name ( 'clients2.info-reader' );
//                 Route::get ( '/billing', "ClientController@updateBilling")->name ( 'clients2.billing' );
//                 Route::get ( '/account/{id}', "ClientController@info")->name ( 'clients2.info' );
//                 Route::get ( '/billing/{id}', "ClientController@infoBilling")->name ( 'clients2.info-billing' );
                              
//                 Route::post ( '/account', "ClientController@storeGroup3")->name ( 'clients2.store' );
//                 Route::post ( '/account/reader', "ClientController@storeReaderGroup3")->name ( 'clients2.storeReader' );
//                 Route::post ( '/account/api', "ClientController@storeApiAccountGroup3")->name ( 'clients2.storeApiAccount' );
//                 Route::post ( '/account/reader/{id}', "ClientController@updateReader")->name ( 'clients2.update-reader' );
//                 Route::post ( '/account/{id}', "ClientController@update")->name ( 'clients2.update' );


//                 // Ajax
//                 Route::group ( [
//                         "prefix" => "/apis"
//                 ], function () {
//                     Route::post ( '/account/delete', 'ClientController@delete')->name ( 'clients2.delete' );
//                     Route::post ( '/account/updateStatus', 'ClientController@updateStatusClient')->name ( 'clients2.updateStatusClient' );
//                     Route::post ( '/account/addCredit', 'ClientController@addCredit')->name ( 'clients2.addCredit' );
//                     Route::post ( '/account/increaseCredit', 'ClientController@increaseCredit')->name ( 'clients2.increaseCredit' );
//                     Route::post ( '/account/descreaseCredit', 'ClientController@descreaseCredit')->name ( 'clients2.descreaseCredit' );
//                     Route::post ( '/account/withdrawCredit', 'ClientController@withdrawCredit')->name ( 'clients2.withdrawCredit' );
//                 } );
//             });
//             Route::get ( '/{id}/price-config', "ClientController@priceConfig")->name ( 'clients2.priceConfig' );
//         } );
//         //---------------------------------

//         // report
//         Route::group([
//                 "prefix" => "/reports"
//         ], function() {
//             Route::get("/campaigns", function() {
//                 return view("admins.reports.campaigns.index");
//             })->name('report-campaign-2.index');
//             Route::get("/campaigns/{user_id}/{campaign_id}", 'ReportController@viewDetailReportCampaign')->name("report-campaign-2.detail");
//             Route::get ('/campaigns/{user_id}/{campaign_id}/export-csv', 'ReportController@exportCSVCampaign')->name("export-campaign-2.csv");
//             Route::get ('/campaigns/{user_id}/{campaign_id}/export-pdf', 'ReportController@exportPDFCampaign')->name("export-campaign-2.pdf");
//             Route::get('/center', "ReportController@viewReportCenter")->name("report-center-2.index");
//         });

//         // transaction history
//         Route::group([
//                 "prefix" => "/transaction-histories"
//         ], function() {
//             Route::get('/client', 'TransactionHistoryController@viewClient')->name("transaction-histories-2.client.index");
//             Route::get('/client/csv', 'TransactionHistoryController@exportClient')->name("transaction-histories-2.client.csv");
//             Route::get("/service-provider", 'TransactionHistoryController@viewServiceProvider')->name("transaction-histories-2.campaigns.index");
//             Route::get("/service-provider/csv", 'TransactionHistoryController@exportCampaignsCSV')->name("transaction-histories-2.campaigns.csv");
//             Route::get("/service-provider/{user_id}/{campaign_id}", 'TransactionHistoryController@getCampaign')->name("transaction-histories-2.campaigns.detail");
//             Route::get("/service-provider/{user_id}/{campaign_id}/csv", 'TransactionHistoryController@exportCSVReportCampaign')->name("transaction-histories-2.campaigns.detail.csv");

//             // api
//             Route::group([
//                     "prefix" => "/apis"
//             ], function() {
//                 Route::get("/client", "TransactionHistoryController@getDataClient")->name('transaction-histories-2.clients');
//                 Route::get("/campaign", "TransactionHistoryController@getDataCampaigns")->name("api.transaction-histories-2.getCampaigns");
//             });
//         });
//     } );
//     // Group 3
//     Route::group ( [
//             'prefix' => 'admin',
//             'middleware' => [
//                     'authentication',
//                     'group3'
//             ]
//     ], function () {
//             //
//             Route::get ( '/', function () {
//                 return redirect ( '/admin/dashboard' );
//             } );
//                 Route::get ( '/dashboard', "DashBoardController@index" );
//                 Route::get ( '/logo', "ClientController@responseLogo");
//                 Route::get ( '/logout', "Auth\LoginController@logout" );
//                 // Subscriber lists
//                 Route::group ( [
//                         'prefix' => '/subscriber-lists'
//                 ], function () {
//                     Route::get ( '/', function () {
//                         return view ( "admins.subscriber-lists.index" );
//                     } );
//                         Route::get ( '/add', function () {
//                             return view ( "admins.subscriber-lists.add" );
//                         } );
//                             Route::get ( '/{id}', "SubscriberListController@get" )->name("subscriber-list.detail");
//                             Route::post ( '/add', "SubscriberListController@create" )->name ( 'subscriberList.create' );
//                 } );
//                 // Subscribers
//                 Route::group ( [
//                         'prefix' => '/subscribers'
//                 ], function () {
//                     Route::get ( '/{id}', "SubscriberController@index" )->name("subscibers.detail");
//                     Route::get ( '{id}/add', "SubscriberController@viewAdd" )->name("subscibers.viewAdd");
//                     Route::get ( '{id}/upload-csv', 'SubscriberController@viewUploadCSV' )->name("subscibers.viewUploadCSV");
//                     Route::post ( '{id}/upload-csv', 'SubscriberController@uploadCSV' )->name("subscibers.uploadCSV");
//                     Route::get ( '{id}/copy-paste', "SubscriberController@viewCopyPaste" )->name("subscibers.viewCopyPaste");
//                     Route::post ( '{id}/copy-paste', 'SubscriberController@copyPaste' )->name("subscibers.copyPaste");
//                     Route::get ( '{id}/mapping', "SubscriberController@mapping" )->name("subscibers.mapping");
//                     Route::post ( '{id}/import-csv', "SubscriberController@importCSV" )->name("subscibers.importCSV");
//                     Route::get ( '{id}/imported', "SubscriberController@imported" )->name("subscibers.imported");
//                     // Update status subscribers
//                     Route::group ( [
//                             'prefix' => '{id}/update'
//                     ], function () {
//                         Route::get ( '/', "SubscriberController@viewUpdate" )->name("subscibers.viewUpdate");
//                         Route::get ( '/upload-csv', "SubscriberController@viewUpdateUploadCSV" );
//                         Route::post ( '/upload-csv', 'SubscriberController@updateUploadCSV' );
//                         Route::get ( '/copy-paste', "SubscriberController@viewUpdateCopyPaste" );
//                         Route::post ( '/copy-paste', "SubscriberController@updateCopyPaste" );
//                         Route::get ( '/mapping', "SubscriberController@updateMapping" );
//                         Route::post ( 'import-csv', "SubscriberController@updateImportCSV" );
//                         Route::get ( '/imported', "SubscriberController@updateImported" );
//                     } );
//                     // Remove subscribers
//                     Route::group ( [
//                             'prefix' => '{id}/remove'
//                     ], function () {
//                         Route::get ( '/', "SubscriberController@viewRemove" )->name("subscibers.viewRemove");
//                         Route::post ( '/', "SubscriberController@destroyWithStatus" )->name("remove-subscriber");
//                         Route::get ( "/return", "SubscriberController@returnRemove" );
//                     } );
//                     // Export subscribers
//                     Route::group ( [
//                             'prefix' => '{id}/export'
//                     ], function () {
//                         Route::get ( '/', "SubscriberController@viewExport" )->name("export-subscriber.view");
//                         Route::post ( '/', "SubscriberController@exportSubsriberWithStatus" )->name("export-subscriber.post");
//                     } );
//                 } );
//                 // Campaigns
//                 Route::group ( [
//                         'prefix' => '/campaigns'
//                 ], function () {
//                     Route::get ( '/', function () {
//                         return view ( 'admins.campaigns.index' );
//                     } )->name ( 'campaign.index' );
//                     Route::get ( '/add', function () {
//                         return view ( 'admins.campaigns.add' );
//                     } );
//                     Route::get ( '/update/{id}', "CampaignController@info" )->name ( 'campaign.info' );
//                     Route::post ( '{id}/update', "CampaignController@update" )->name ( 'campaign.update' );
//                 });
//                 // Campaign
//                 Route::group ( [
//                         'prefix' => '/campaign'
//                 ], function () {
//                     Route::get ( '/create', 'CampaignController@create' );
//                     Route::post ( '/', 'CampaignController@store' )->name ( 'campaign.store' );
//                 } );

//                 Route::group([
//                         "prefix" => "/reports"
//                 ], function() {
//                     Route::get("/campaigns", function() {
//                         return view("admins.reports.campaigns.index");
//                     })->name('report-campaign.index');
//                     Route::get("/campaigns/{user_id}/{campaign_id}", 'ReportController@viewDetailReportCampaign')->name("report-campaign.detail");
//                     Route::get ('/campaigns/{user_id}/{campaign_id}/export-csv', 'ReportController@exportCSVCampaign')->name("export-campaign.csv");
//                     Route::get ('/campaigns/{user_id}/{campaign_id}/export-pdf', 'ReportController@exportPDFCampaign')->name("export-campaign.pdf");
//                     Route::get('/center', "ReportController@viewReportCenter")->name("report-center.index");
//                     Route::get("/campaigns/{user_id}/{campaign_id}/{url_id}", 'ReportController@viewDetailShortLinks')->name("short-links.detail");
//                 });
//                 // Templates
//                 Route::group([
//                         "prefix" => "/templates"
//                 ], function() {
//                     Route::get ( '/', function () {
//                         return view ( 'admins.templates.index' );
//                     } )->name ( 'templates.index' );
//                     Route::get ( '/create', function () {
//                         return view ( 'admins.templates.add', ['template' => null] );
//                     })->name ( 'templates.create' );
//                     Route::get ( '/update/{id}', "TemplateController@info" )->name ( 'templates.info' );
//                     Route::post ( '{id}/update', "TemplateController@update" )->name ( 'templates.update' );
//                     Route::post ( '/', 'TemplateController@store' )->name ( 'templates.store' );
//                 });
//                 // Download
//                 Route::group ( [
//                         "prefix" => "downloads"
//                 ], function () {
//                     Route::get ( '/subscribers/{key}/{hash}', "SubscriberController@downloadCSV" );
//                     Route::get ( '/reports/{hash}', "ReportController@downloadFile" );
//                 } );

//                 Route::get ( '/inbound-messages', "SettingsController@inboundMessages" )->name('inboundMessages');

//                 Route::group([
//                     "prefix" => "/notification-settings"
//                 ], function() {
//                     Route::get ('/', 'NotificationSettingController@index')->name("notification-settings.index");
//                     Route::post('/add', 'NotificationSettingController@store')->name("notification-settings.add");
//                 });

//                 Route::group([
//                     "prefix" => "/tokens"
//                 ], function() {
//                     Route::get ('/', 'TokenController@index');
//                     Route::get ('/create', 'TokenController@create')->name('tokens.create');
//                 });
//         } );
//     // Group 4
//     Route::group ( [
//             'prefix' => 'reader',
//             'middleware' => [
//                     'authentication',
//                     'group4'
//             ]
//     ], function () {
//         Route::get ( '/dashboard', "DashBoardController@index" );
//         Route::get ( '/logout', "Auth\LoginController@logout" );
//         // Subscriber lists
//         Route::group ( [
//                 'prefix' => '/subscriber-lists'
//         ], function () {
//             Route::get ( '/', function () {
//                 return view ( "admins.subscriber-lists.index" );
//             } );
//             Route::get ( '/add', function () {
//                 return view ( "admins.subscriber-lists.add" );
//             } );
//             Route::get ( '/{id}', "SubscriberListController@get" );
//             Route::post ( '/add', "SubscriberListController@create" )->name ( 'subscriberList.create' );
//         } );
//         // Subscribers
//         Route::group ( [
//                 'prefix' => '/subscribers'
//         ], function () {
//             Route::get ( '/{id}', "SubscriberController@index" )->name('subscribers_list_group4.info');

//             // Export subscribers
//             Route::group ( [
//                     'prefix' => '{id}/export'
//             ], function () {
//                 Route::get ( '/', "SubscriberController@viewExport" )->name("export-subscriber-4.view");
//                 Route::post ( '/', "SubscriberController@exportSubsriberWithStatus" )->name("export-subscriber-4.post");
//             } );
//         } );
//         // Campaigns
//         Route::group ( [
//                 'prefix' => '/campaigns'
//         ], function () {
//             Route::get ( '/', function () {
//                 return view ( 'admins.campaigns.index' );
//             } )->name ( 'campaign4.index' );
//             Route::get ( '/update/{id}', "CampaignController@info" )->name ( 'campaign4.info' );
//         } );
//         // Reports
//         Route::group([
//                 "prefix" => "/reports"
//         ], function() {
//             Route::get("/campaigns", function() {
//                 return view("admins.reports.campaigns.index");
//             })->name('report-campaign-4.index');
//             Route::get("/campaigns/{user_id}/{campaign_id}", function($userID, $campaignID) {
//                 return view("admins.reports.campaigns.detail", ["user_id" => $userID, "campaign_id" => $campaignID]);
//             })->name("report-campaign-4.detail");
//             Route::get ('/campaigns/{user_id}/{campaign_id}/export-csv', 'ReportController@exportCSVCampaign')->name("export-campaign-4.csv");
//             Route::get ('/campaigns/{user_id}/{campaign_id}/export-pdf', 'ReportController@exportPDFCampaign')->name("export-campaign-4.pdf");
//             Route::get('/center', "ReportController@viewReportCenter")->name("report-center-4.index");
//         });
//         // Download
//         Route::group ( [
//                 "prefix" => "downloads"
//         ], function () {
//             Route::get ( '/subscribers/{key}/{hash}', "SubscriberController@downloadCSV" );
//             Route::get ( '/reports/{hash}', "ReportController@downloadFile" );
//         } );
//     } );
// });
