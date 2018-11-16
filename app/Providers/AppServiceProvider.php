<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		//
		Schema::defaultStringLength ( 191 );
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->app->singleton ( \App\Repositories\Auth\IAuthenticationRepository::class, \App\Repositories\Auth\AuthenticationRepository::class );
		$this->app->singleton ( \App\Services\Auth\IAuthenticationService::class, \App\Services\Auth\AuthenticationService::class );
		$this->app->singleton ( \App\Repositories\SubscriberLists\ISubscriberListRepository::class, \App\Repositories\SubscriberLists\SubscriberListRepository::class);
		$this->app->singleton ( \App\Services\SubscriberLists\ISubscriberListService::class, \App\Services\SubscriberLists\SubscriberListService::class);
		$this->app->singleton ( \App\Repositories\Subscribers\ISubscriberRepository::class, \App\Repositories\Subscribers\SubscriberRepository::class );
		$this->app->singleton ( \App\Services\Subscribers\ISubscriberService::class, \App\Services\Subscribers\SubscriberService::class );
		$this->app->singleton ( \App\Repositories\CustomFields\ICustomFieldRepository::class, \App\Repositories\CustomFields\CustomFieldRepository::class );
		$this->app->singleton ( \App\Services\CustomFields\ICustomFieldService::class, \App\Services\CustomFields\CustomFieldService::class );
		$this->app->singleton ( \App\Repositories\Templates\ITemplateRepository::class, \App\Repositories\Templates\TemplateRepository::class );
		$this->app->singleton ( \App\Services\Templates\ITemplateService::class, \App\Services\Templates\TemplateService::class );
		//
		$this->app->singleton ( \App\Services\SMS\ISMSService::class, \App\Services\SMS\SMSService::class );
		$this->app->singleton ( \App\Services\MailServices\IMailService::class, \App\Services\MailServices\MailService::class );
		// Campaign common
		$this->app->singleton( \App\Repositories\Campaign\ICampaignRepository::class, \App\Repositories\Campaign\CampaignRepository::class);
		$this->app->singleton( \App\Services\Campaign\ICampaignService::class, \App\Services\Campaign\CampaignService::class);
		$this->app->singleton( \App\Repositories\Campaign\ICampaignRecipientsRepository::class, \App\Repositories\Campaign\CampaignRecipientsRepository::class);
		$this->app->singleton( \App\Services\Campaign\ICampaignRecipientsService::class, \App\Services\Campaign\CampaignRecipientsService::class);
		$this->app->singleton( \App\Repositories\Campaign\IQueueRepository::class, \App\Repositories\Campaign\QueueRepository::class);
		$this->app->singleton( \App\Services\Campaign\IQueueService::class, \App\Services\Campaign\QueueService::class);
		$this->app->singleton( \App\Services\ShortLinks\IShortLinkService::class, \App\Services\ShortLinks\ShortLinkService::class);
		$this->app->singleton ( \App\Repositories\Campaign\ICampaignLinksRepository::class, \App\Repositories\Campaign\CampaignLinksRepository::class );
		$this->app->singleton ( \App\Services\Campaign\ICampaignLinkService::class, \App\Services\Campaign\CampaignLinkService::class );
		$this->app->singleton ( \App\Repositories\Campaign\ICampaignStatsLinkRepository::class, \App\Repositories\Campaign\CampaignStatsLinkRepository::class );
		$this->app->singleton ( \App\Services\Campaign\ICampaignStatsLinkService::class, \App\Services\Campaign\CampaignStatsLinkService::class );
		// Settings common
		$this->app->singleton ( \App\Repositories\Settings\IServiceProviderRepository::class, \App\Repositories\Settings\ServiceProviderRepository::class );
		$this->app->singleton ( \App\Services\Settings\IServiceProviderService::class, \App\Services\Settings\ServiceProviderService::class );
		$this->app->singleton ( \App\Repositories\Settings\IPreferredServiceProviderRepository::class, \App\Repositories\Settings\PreferredServiceProviderRepository::class );
		$this->app->singleton ( \App\Services\Settings\IPreferredServiceProviderService::class, \App\Services\Settings\PreferredServiceProviderService::class );
		$this->app->singleton ( \App\Repositories\Settings\IMobilePatternRepository::class, \App\Repositories\Settings\MobilePatternRepository::class );
		$this->app->singleton ( \App\Services\Settings\IMobilePatternService::class, \App\Services\Settings\MobilePatternService::class );
		$this->app->singleton ( \App\Repositories\Settings\IMCCMNCRepository::class, \App\Repositories\Settings\MCCMNCRepository::class );
		$this->app->singleton ( \App\Services\Settings\IMCCMNCService::class, \App\Services\Settings\MCCMNCService::class );
		$this->app->singleton ( \App\Repositories\Settings\ICountryRepository::class, \App\Repositories\Settings\CountryRepository::class );
		$this->app->singleton ( \App\Services\Settings\ICountryService::class, \App\Services\Settings\CountryService::class );
		$this->app->singleton ( \App\Services\Settings\IConfigurationService::class, \App\Services\Settings\ConfigurationService::class );
		$this->app->singleton ( \App\Repositories\Settings\IConfigurationRepository::class, \App\Repositories\Settings\ConfigurationRepository::class );
		$this->app->singleton ( \App\Services\Settings\IInboundConfigService::class, \App\Services\Settings\InboundConfigService::class );
		$this->app->singleton ( \App\Repositories\Settings\IInboundConfigRepository::class, \App\Repositories\Settings\InboundConfigRepository::class );
		// Report common
		$this->app->singleton ( \App\Repositories\Reports\IReportListSummaryRepository::class, \App\Repositories\Reports\ReportListSummaryRepository::class );
		// Client
		$this->app->singleton( \App\Services\Clients\IClientService::class, \App\Services\Clients\ClientService::class );
		$this->app->singleton( \App\Repositories\Clients\IClientRepository::class, \App\Repositories\Clients\ClientRepository::class );
		$this->app->singleton( \App\Services\Clients\IPriceConfigurationService::class, \App\Services\Clients\PriceConfigurationService::class );
		$this->app->singleton( \App\Repositories\Clients\IPriceConfigurationRepository::class, \App\Repositories\Clients\PriceConfigurationRepository::class );
		// report
		$this->app->singleton( \App\Repositories\Reports\IReportCampaignReponsitory::class, \App\Repositories\Reports\ReportCampaignReponsitory::class );
		$this->app->singleton( \App\Services\Reports\IReportService::class, \App\Services\Reports\ReportService::class );
		// Report Center
		$this->app->singleton( \App\Repositories\Reports\IReportCenterReponsitory::class, \App\Repositories\Reports\ReportCenterReponsitory::class );
		// Transaction
		$this->app->singleton( \App\Services\Transactions\ITransactionService::class, \App\Services\Transactions\TransactionService::class );
		$this->app->singleton( \App\Repositories\Transactions\ITransactionRepository::class, \App\Repositories\Transactions\TransactionRepository::class );
		// Transaction History
		$this->app->singleton( \App\Repositories\TransactionHistories\ITransactionHistoryRepository::class, \App\Repositories\TransactionHistories\TransactionHistoryRepository::class );
		$this->app->singleton( \App\Services\TransactionHistories\ITransactionHistoryService::class, \App\Services\TransactionHistories\TransactionHistoryService::class );
		// Inbound Messages
		$this->app->singleton( \App\Repositories\InboundMessages\IInboundMessagesRepository::class, \App\Repositories\InboundMessages\InboundMessagesRepository::class );
		$this->app->singleton( \App\Services\InboundMessages\IInboundMessagesService::class, \App\Services\InboundMessages\InboundMessagesService::class );
		// Forgot Password
		$this->app->singleton( \App\Repositories\Auth\IForgotPasswordRepository::class, \App\Repositories\Auth\ForgotPasswordRepository::class );
		// notification settings
		$this->app->singleton( \App\Repositories\NotificationSettings\INotificationSettingRepository::class, \App\Repositories\NotificationSettings\NotificationSettingRepository::class );
		$this->app->singleton( \App\Services\NotificationSettings\INotificationSettingService::class, \App\Services\NotificationSettings\NotificationSettingService::class );
		// Campaign Paused
		$this->app->singleton( \App\Repositories\Campaign\ICampaignPausedRepository::class, \App\Repositories\Campaign\CampaignPausedRepository::class );
		$this->app->singleton( \App\Services\Campaign\ICampaignPausedService::class, \App\Services\Campaign\CampaignPausedService::class );
		// TM SMS Report
		$this->app->singleton( \App\Repositories\Reports\ITMSMSReportReponsitory::class, \App\Repositories\Reports\TMSMSReportReponsitory::class );
		$this->app->singleton( \App\Services\Reports\ITMSMSReportService::class, \App\Services\Reports\TMSMSReportService::class );
		// Router Mobile Report
		$this->app->singleton( \App\Repositories\Reports\IRouteMobileReportReponsitory::class, \App\Repositories\Reports\RouteMobileReportReponsitory::class );
		$this->app->singleton( \App\Services\Reports\IRouterMobileReportService::class, \App\Services\Reports\RouterMobileReportService::class );
		// Token 
		$this->app->singleton( \App\Services\Auth\ITokenService::class, \App\Services\Auth\TokenService::class );
		$this->app->singleton( \App\Repositories\Auth\ITokenRepository::class, \App\Repositories\Auth\TokenRepository::class );
		// Infobip Report Service 
		$this->app->singleton( \App\Repositories\Reports\IInfobipReportReponsitory::class, \App\Repositories\Reports\InfobipReportReponsitory::class );
		$this->app->singleton( \App\Services\Reports\IInfobipReportService::class, \App\Services\Reports\InfobipReportService::class );
		// Handel status sms
		$this->app->singleton(\App\Services\SMS\IHandleStatusSms::class, \App\Services\SMS\HandleStatusSms::class);
		// Response send sms async
		$this->app->singleton(\App\Services\SMS\IResponseSendSms::class, \App\Services\SMS\ResponseSendSms::class);
		// sent campaign service
		$this->app->singleton(\App\Services\Campaign\ISentCampaignService::class, \App\Services\Campaign\SentCampaignService::class);
	}
}