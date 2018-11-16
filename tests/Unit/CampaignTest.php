<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Auth;

class CampaignTest extends TestCase
{
    /**
     * A basic test create campaign.
     *
     * @return void
     */
    public function testCreateCampaign()
    {
        $params = [
            'campaign_link_id' => null,
            'isPersonalize' => false,
            'name' => 'test Campaign',
            'list_id' => [32],
            'sender' => 'Verify',
            'language' => 'ASCII',
            'message' => 'test campaign',
            'schedule_type' => 'NOT_SCHEDULED',
            'valid_period' => '24',
            'test_recipients' => null
        ];

        $user = Auth::loginUsingId(3);
        $response = $this->json('POST', env('APP_URL') . '/admin/campaign', $params);
        
        $response->assertSessionHas('success');
        $response->assertRedirect('admin/campaigns');
    }
}
