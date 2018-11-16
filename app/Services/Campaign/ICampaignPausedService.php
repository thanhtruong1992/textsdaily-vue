<?php

namespace App\Services\Campaign;

interface ICampaignPausedService {
    public function updateOrCreate(array $attributes);
    public function removeCampaignPaused($id);
}