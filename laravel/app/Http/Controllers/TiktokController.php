<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdsGroup;
use App\Models\AdsGroupsReport;
use App\Models\AdsReport;
use App\Models\Campaign;
use App\Models\CampaignsReport;
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
            response()->json($response)
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
            if ($response->successful()) {
                $groups = $response['data']['list'];
                foreach ($groups as $groupData) {
                    $groupData = $this->convertArraysToJson($groupData);
                    AdsGroup::create($groupData);
                }
                DB::commit();
                return response()->json($groups);
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

    public function getAds(Request $request)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://business-api.tiktok.com/open_api/v1.3/ad/get/';
            $response = Http::withHeaders([
                'Access-Token' => config('services.tiktok.access_token'),
            ])->get($apiEndpoint, ['advertiser_id' => config('services.tiktok.advertiser_id')]);
            if ($response->successful()) {
                $ads = $response['data']['list'];
                foreach ($ads as $adData) {
                    $adData = $this->convertArraysToJson($adData);
                    Ad::create($adData);
                }
                DB::commit();
                return response()->json($ads);
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

    public function getAdsReport(Request $request)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://business-api.tiktok.com/open_api/v1.3/report/integrated/get';
            $response = Http::withHeaders([
                'Access-Token' => config('services.tiktok.access_token'),
            ])->get($apiEndpoint, [
                'advertiser_id' => config('services.tiktok.advertiser_id'),
                'metrics' => json_encode(["spend", "impressions","total_purchase_value",
                    "total_onsite_shopping_value","conversion_rate","clicks","offline_shopping_events_value"]),
                'data_level' => 'AUCTION_AD',
                'dimensions' => json_encode(["ad_id","stat_time_day"]),
                'service_type' => 'AUCTION',
                'report_type' => 'BASIC',
                'page_size' => 50,
                'page'  =>  1,
                'start_date' => '2023-03-02',
                'end_date' => '2023-04-01'
                ]);
            if ($response->successful()) {
                $adsReport = $response['data']['list'];
                foreach ($adsReport as $adReportData) {
                    $adReport = [];
                    $adReport['conversion_rate'] = $adReportData['metrics']['conversion_rate'];
                    $adReport['total_purchase_value'] = $adReportData['metrics']['total_purchase_value'];
                    $adReport['offline_shopping_events_value'] = $adReportData['metrics']['offline_shopping_events_value'];
                    $adReport['impressions'] = $adReportData['metrics']['impressions'];
                    $adReport['clicks'] = $adReportData['metrics']['clicks'];
                    $adReport['total_onsite_shopping_value'] = $adReportData['metrics']['total_onsite_shopping_value'];
                    $adReport['spend'] = $adReportData['metrics']['spend'];
                    $adReport['stat_time_day'] = $adReportData['dimensions']['stat_time_day'];
                    $adReport['ad_id'] = $adReportData['dimensions']['ad_id'];
                    AdsReport::create($adReport);
                }
                DB::commit();
                return response()->json($adsReport);
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

    public function getAdsGroupsReport(Request $request)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://business-api.tiktok.com/open_api/v1.3/report/integrated/get';
            $response = Http::withHeaders([
                'Access-Token' => config('services.tiktok.access_token'),
            ])->get($apiEndpoint, [
                'advertiser_id' => config('services.tiktok.advertiser_id'),
                'metrics' => json_encode(["spend", "impressions","total_purchase_value",
                    "total_onsite_shopping_value","conversion_rate","clicks","offline_shopping_events_value"]),
                'data_level' => 'AUCTION_ADGROUP',
                'dimensions' => json_encode(["adgroup_id","stat_time_day"]),
                'service_type' => 'AUCTION',
                'report_type' => 'BASIC',
                'page_size' => 50,
                'page'  =>  1,
                'start_date' => '2023-03-02',
                'end_date' => '2023-04-01'
            ]);
            if ($response->successful()) {
                $adsGroupsReport = $response['data']['list'];
                foreach ($adsGroupsReport as $adGroupReportData) {
                    $adGroupReport = [];
                    $adGroupReport['conversion_rate'] = $adGroupReportData['metrics']['conversion_rate'];
                    $adGroupReport['total_purchase_value'] = $adGroupReportData['metrics']['total_purchase_value'];
                    $adGroupReport['offline_shopping_events_value'] = $adGroupReportData['metrics']['offline_shopping_events_value'];
                    $adGroupReport['impressions'] = $adGroupReportData['metrics']['impressions'];
                    $adGroupReport['clicks'] = $adGroupReportData['metrics']['clicks'];
                    $adGroupReport['total_onsite_shopping_value'] = $adGroupReportData['metrics']['total_onsite_shopping_value'];
                    $adGroupReport['spend'] = $adGroupReportData['metrics']['spend'];
                    $adGroupReport['stat_time_day'] = $adGroupReportData['dimensions']['stat_time_day'];
                    $adGroupReport['ad_group_id'] = $adGroupReportData['dimensions']['adgroup_id'];
                    AdsGroupsReport::create($adGroupReport);
                }
                DB::commit();
                return response()->json($adsGroupsReport);
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

    public function getCampaignsReport(Request $request)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://business-api.tiktok.com/open_api/v1.3/report/integrated/get';
            $response = Http::withHeaders([
                'Access-Token' => config('services.tiktok.access_token'),
            ])->get($apiEndpoint, [
                'advertiser_id' => config('services.tiktok.advertiser_id'),
                'metrics' => json_encode(["spend", "impressions","total_purchase_value",
                    "total_onsite_shopping_value","conversion_rate","clicks","offline_shopping_events_value"]),
                'data_level' => 'AUCTION_CAMPAIGN',
                'dimensions' => json_encode(["campaign_id","stat_time_day"]),
                'service_type' => 'AUCTION',
                'report_type' => 'BASIC',
                'page_size' => 50,
                'page'  =>  1,
                'start_date' => '2023-03-02',
                'end_date' => '2023-04-01'
            ]);
            if ($response->successful()) {
                $campaignsReport = $response['data']['list'];
                foreach ($campaignsReport as $campaignReportData) {
                    $campaignReport = [];
                    $campaignReport['conversion_rate'] = $campaignReportData['metrics']['conversion_rate'];
                    $campaignReport['total_purchase_value'] = $campaignReportData['metrics']['total_purchase_value'];
                    $campaignReport['offline_shopping_events_value'] = $campaignReportData['metrics']['offline_shopping_events_value'];
                    $campaignReport['impressions'] = $campaignReportData['metrics']['impressions'];
                    $campaignReport['clicks'] = $campaignReportData['metrics']['clicks'];
                    $campaignReport['total_onsite_shopping_value'] = $campaignReportData['metrics']['total_onsite_shopping_value'];
                    $campaignReport['spend'] = $campaignReportData['metrics']['spend'];
                    $campaignReport['stat_time_day'] = $campaignReportData['dimensions']['stat_time_day'];
                    $campaignReport['campaign_id'] = $campaignReportData['dimensions']['campaign_id'];
                    CampaignsReport::create($campaignReport);
                }
                DB::commit();
                return response()->json($campaignsReport);
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

    function convertArraysToJson($data) {
        foreach ($data as &$value) {
            if (is_array($value) || is_object($value)) {
                // If the value is an array, convert it to a JSON string
                $value = json_encode($value);
            }
        }
        return $data;
    }
}
