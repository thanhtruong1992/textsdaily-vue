<?php
namespace App\Services\ShortLinks;

use GuzzleHttp\Client;
use App\Services\BaseService;
use Illuminate\Support\Facades\Log;

class ShortLinkService extends BaseService implements IShortLinkService
{
    protected $client;
    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept' => 'application/json',
            ],
            'verify' => false
        ]);
    }

    public function shortLink($link)
    {
        try {
            $url = env('BITLY_API_SHORT_LINK', 'https://api-ssl.bitly.com/v4/shorten');
            $data = [
                "group_guid" => env('BITLY_GROUP_ID'),
                "domain" => env('BITLY_DOMAIN'),
                "long_url" => $link,
            ];
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env('BITLY_ACCESS_TOKEN'),
            ];

            $response = $this->client->post($url, ['headers' => $headers, 'json' => $data]);
            $result = json_decode($response->getBody()->getContents());

            if (in_array($response->getStatusCode(), [200, 201])) {
                return $this->success(['id' => $result->id, 'shortLink' => $result->link]);
            }

            return $this->fail();
        } catch (\Exception $e) {
            return $this->fail();
        }
        
    }

    /**
     * fn short link of dct short link
     * @param array $data
     */
    public function shortLinkDCT($data)
    {
        try {
            $url = env('DCT_API_SHORT_LINK');
            $data = (object) $data;
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('DCT_TOKEN_SHORT_LINK'),
            ];

            $response = $this->client->post($url, ['headers' => $headers, 'json' => $data]);
            $result = json_decode($response->getBody()->getContents());

            if (in_array($response->getStatusCode(), [200, 201])) {
                $result = $result->data;
                if(empty($result) || $result == null) {
                    Log::error ( 'Error Short LInk SendSMS-' . date ( 'Y-m-d' ) . " " .  json_decode(null, true) );
                    return $this->fail();
                }
                return $this->success(['id' => $result->uuid, 'short_link' => $result->short_link]);
            }
            Log::error ( 'Error Short LInk SendSMS-' . date ( 'Y-m-d' ) . " " .  json_decode($result, true) );
            return $this->fail();
        } catch (\Exception $e) {
            Log::error ( 'Error Short LInk Exception SendSMS-' . date ( 'Y-m-d' ) . " " .  $e->getMessage() );
            return $this->fail();
        }
        
    }

    public function shortLinkGoogle($link)
    {
        try {
            $url = env('URL_SHORT_LINK_GOOGLE') . "?key=" . env('KEY_SHORT_LINK_GOOGLE');
            $data = [
                "longUrl" => $link
            ];
            $headers = [
                'Content-type' => ' application/json; charset=utf-8',
                'Accept' => 'application/json',
            ];

            $response = $this->client->post($url, ['json' => $data]);
            $result = json_decode($response->getBody()->getContents());
            if ($response->getStatusCode() == 200) {
                return $this->success(['shortLink' => $result->id]);
            }

            return $this->fail();
        } catch (\Exception $e) {
            return $this->fail();
        }
        
    }
}
