<?php
namespace App\Repositories\Campaign;

use App\Repositories\BaseRepository;

class CampaignPausedRepository extends BaseRepository implements ICampaignPausedRepository{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\CampaignPaused";
    }
}