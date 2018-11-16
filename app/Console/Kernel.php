<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param \Illuminate\Console\Scheduling\Schedule $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule) {
		// send campaign
		$schedule->call('App\Http\Controllers\CampaignController@cronSendCampaign')->everyMinute();
	    // Report sms message
	    $schedule->call('App\Http\Controllers\CampaignController@cronGetDeliveryReports')->everyMinute();
	    // Update status send mail
	    $schedule->call('App\Http\Controllers\CampaignController@sendAgainCampaugnPaused')->everyMinute();
	    // Detect country, network for subscribers
	    $schedule->call('App\Http\Controllers\SubscriberController@cronDetectSubscribers')->everyMinute();
	    // Monthly reset credits
	    $schedule->call('App\Http\Controllers\ClientController@transferCreditsMonthly')->monthly();
	    // Rerport Center
	    $schedule->call('App\Http\Controllers\ReportController@cronJobReportCenter')->everyMinute();
	    // Daily report
	    $schedule->call('App\Http\Controllers\ReportController@cronAutoReport')->daily();
	    $schedule->call('App\Http\Controllers\ReportController@cronSendEmailReport')->everyMinute();
	    // Receive sms message
	    $schedule->call('App\Http\Controllers\CampaignController@cronGetReceivedMessages')->cron('*/2 * * * * *');
	}

	/**
	 * Register the Closure based commands for the application.
	 *
	 * @return void
	 */
	protected function commands() {
		require base_path ( 'routes/console.php' );
	}
}
