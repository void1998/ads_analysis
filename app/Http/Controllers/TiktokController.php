<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TiktokController extends Controller
{
    public function getCampaigns(Request $request)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://business-api.tiktok.com/open_api/v1.3/campaign/get/';
            $response = Http::withHeaders([
                'Access-Token' => config('services.tiktok.access_token'),
            ])->get($apiEndpoint, ['advertiser_id' => config('services.tiktok.advertiser_id')]);
            if ($response->successful()) {
                $campaigns = $response['data']['list'];
                foreach ($campaigns as $campaignData) {
                    $campaignData['special_industries'] = json_encode($campaignData['special_industries']);
                    Campaign::create($campaignData);
                }
                DB::commit();
                return response()->json($response);
            } else {
                return response()->json([
                    'message' => 'Failed to fetch TikTok campaign information.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            // An exception occurred, so we roll back the transaction and return an error response
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred while fetching TikTok campaign information.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAdsGroups(Request $request)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://business-api.tiktok.com/open_api/v1.3/adgroup/get/';
            $response = Http::withHeaders([
                'Access-Token' => config('services.tiktok.access_token'),
            ])->get($apiEndpoint, ['advertiser_id' => config('services.tiktok.advertiser_id')]);
            return response()->json($response);
            if ($response->successful()) {
                $campaigns = $response['data']['list'];
                foreach ($campaigns as $campaignData) {
                    $campaignData['special_industries'] = json_encode($campaignData['special_industries']);
                    Campaign::create($campaignData);
                }
                DB::commit();
                return response()->json($campaigns);
            } else {
                return response()->json([
                    'message' => 'Failed to fetch TikTok campaign information.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            // An exception occurred, so we roll back the transaction and return an error response
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred while fetching TikTok campaign information.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getAccessToken()
    {
        // Fetch campaign information from the TikTok API
        $tiktokApiKey = config('services.tiktok.api_key');
        $tiktokApiSecret = config('services.tiktok.api_secret');
        $apiEndpoint = 'https://business-api.tiktok.com/open_api/v1.3/oauth2/access_token/';

        $response = Http::withHeaders([
            'X-ADNW-API-KEY' => $tiktokApiKey,
            'X-ADNW-API-SECRET' => $tiktokApiSecret,
        ])->get($apiEndpoint);

        if ($response->successful()) {
            $campaigns = $response->json();
            return response()->json($campaigns);
        } else {
            return response()->json([
                'message' => 'Failed to fetch TikTok campaign information.',
            ], $response->status());
        }
    }
}
