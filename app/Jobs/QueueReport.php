<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Campaign\IQueueService;
use App\Services\Campaign\ICampaignService;

class QueueReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

    public $campaignID;
    public $userID;
    public $queues;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userID, $campaignID, $queues)
    {
        $this->userID = $userID;
        $this->campaignID = $campaignID;
        $this->queues = $queues;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(IQueueService $queueService, ICampaignService $campaignService)
    {
        $result = $queueService->runQueueDeliveryReport($this->userID, $this->campaignID, $this->queues);
        if($result->status == true) {
            $campaignService->finsihedQueueReport($result);
        }
    }
}
