<?php

namespace App\Services\Campaign;

use App\Services\BaseService;
use App\Repositories\Campaign\ICampaignPausedRepository;
use Carbon\Carbon;

class CampaignPausedService extends BaseService implements ICampaignPausedService{
    protected $campaignPausedRepo;
    protected $campaignService;
    public function __construct(ICampaignPausedRepository $campaignPausedRepo) {
        $this->campaignPausedRepo = $campaignPausedRepo;
    }

    public function updateOrCreate(array $attributes) {
        try {
            $campaignPaused = $this->campaignPausedRepo->findWhere($attributes)->first();
            $attributes["tracking_updated_at"] = Carbon::now()->addMinute(2)->toDateTimeString();

            if(!empty($campaignPaused)) {
                $campaignPaused = (object) $campaignPaused->toArray();
                // update
                $attributes["count"] = $campaignPaused->count + 1;
                return $this->campaignPausedRepo->update($attributes, $campaignPaused->id);
            } else {
                $attributes["count"] = 1;
                // create
                return $this->campaignPausedRepo->create($attributes);
            }
        }catch (\Exception $e) {
            //error
            return $e->getMessage();
        }
    }

    public function removeCampaignPaused($id) {
        return $this->campaignPausedRepo->delete($id);
    }
}