<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Campaign\QueueService;
use Illuminate\Support\Facades\Log;
use App\Services\Campaign\IQueueService;
use App\Services\Campaign\ICampaignService;

class SendCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $campaign;
    public $queues;
    public $times;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    // public $timeout = 180;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $campaign, $queues, $times = 0)
    {
        $this->user = $user;
        $this->campaign = $campaign;
        $this->queues = $queues;
        $this->times = $times;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(IQueueService $queueService, ICampaignService $campaignService)
    {
        // Log::error('test log ' . $this->user . " " . $this->campaign . " " . $this->queues);
        $result = $queueService->queuSendSMS($this->user, $this->campaign, $this->queues, $this->times);
        if($result != null) {
            return $campaignService->queueSentCampaign($result);
        }
        return;
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
    }
}
