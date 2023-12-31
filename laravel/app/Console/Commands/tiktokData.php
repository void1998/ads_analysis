<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Models\AdsGroup;
use App\Models\AdsGroupsReport;
use App\Models\SnapchatCampaignReport;
use App\Models\AdsReport;
use App\Models\Campaign;
use App\Models\CampaignsReport;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class tiktokData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tiktok-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
//        Log::info("tiktok data sync start");
//        $campaignsCount = $this->getCampaigns();
//        Log::info('campaignsCount:'.$campaignsCount);
//        $adsGroupsCount = $this->getAdsGroups();
//        Log::info('adsGroupsCount:'.$adsGroupsCount);
//        $adsCount = $this->getAds();
//        Log::info('adsCount:'.$adsCount);
        $campaignsReportsCount = $this->getDataWithRangeDate();
        Log::info('campaignsReportsCount:'.$campaignsReportsCount);
//        $adsGroupsReportsCount = $this->getAdsGroupsReport();
//        Log::info('adGroupsReportsCount:'.$adsGroupsReportsCount);
//        $adsReportsCount = $this->getAdsReport();
//        Log::info('adsReportsCount:'.$adsReportsCount);
//        Log::info("tiktok data sync end");
    }


    public function getCampaigns()
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://business-api.tiktok.com/open_api/v1.3/campaign/get/';
            $response = Http::withHeaders([
                'Access-Token' => config('services.tiktok.access_token'),
            ])->get($apiEndpoint, ['advertiser_id' => config('services.tiktok.advertiser_id'),
                "filtering" => [
                    "creation_filter_start_time"=>Carbon::now()->format('Y-m-d H:i:s'),
                    "creation_filter_end_time"=>Carbon::yesterday()->format('Y-m-d H:i:s')
                ]
            ]);

            if ($response->successful()) {
                $campaigns = $response['data']['list'];
                foreach ($campaigns as $campaignData) {
                    $campaignData['special_industries'] = json_encode($campaignData['special_industries']);
                    $exists = Campaign::where('campaign_id',$campaignData['campaign_id'])->first();
                    if(!$exists)
                    Campaign::create($campaignData);
                }
                DB::commit();
                return count($campaigns);
            } else {
                return response()->json([
                    'message' => 'Failed to fetch TikTok campaign information.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            // An exception occurred, so we roll back the transaction and return an error response
            DB::rollBack();
            Log::info($e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching TikTok campaign information.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAdsGroups()
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://business-api.tiktok.com/open_api/v1.3/adgroup/get/';
            $response = Http::withHeaders([
                'Access-Token' => config('services.tiktok.access_token'),
            ])->get($apiEndpoint, ['advertiser_id' => config('services.tiktok.advertiser_id'),
                "filtering" => [
                    "creation_filter_start_time"=>Carbon::now()->format('Y-m-d H:i:s'),
                    "creation_filter_end_time"=>Carbon::yesterday()->format('Y-m-d H:i:s')
                ]

            ]);
            if ($response->successful()) {
                $groups = $response['data']['list'];
                foreach ($groups as $groupData) {
                    $groupData = $this->convertArraysToJson($groupData);
                    $exists = AdsGroup::where('adgroup_id',$groupData['adgroup_id'])->first();
                    if(!$exists)
                    AdsGroup::create($groupData);
                }
                DB::commit();
                return count($groups);
            } else {
                return response()->json([
                    'message' => 'Failed to fetch TikTok campaign information.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            // An exception occurred, so we roll back the transaction and return an error response
            DB::rollBack();
            Log::info($e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching TikTok campaign information.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAds()
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://business-api.tiktok.com/open_api/v1.3/ad/get/';
            $response = Http::withHeaders([
                'Access-Token' => config('services.tiktok.access_token'),
            ])->get($apiEndpoint, ['advertiser_id' => config('services.tiktok.advertiser_id'),
                "filtering" => [
                    "creation_filter_start_time"=>Carbon::now()->format('Y-m-d H:i:s'),
                    "creation_filter_end_time"=>Carbon::yesterday()->format('Y-m-d H:i:s')
                ],
            ]);
            if ($response->successful()) {
                $ads = $response['data']['list'];
                foreach ($ads as $adData) {
                    $adData = $this->convertArraysToJson($adData);
                    $exists = Ad::where('ad_id',$adData['ad_id'])->first();
                    if(!$exists)
                    Ad::create($adData);
                }
                DB::commit();
                return count($ads);
            } else {
                return response()->json([
                    'message' => 'Failed to fetch TikTok campaign information.',
                ], $response->status());
            }

        } catch (\Exception $e) {
            // An exception occurred, so we roll back the transaction and return an error response
            DB::rollBack();
            Log::info($e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching TikTok campaign information.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAdsReport($start_date,$end_date)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://business-api.tiktok.com/open_api/v1.3/report/integrated/get';
            $response = Http::withHeaders([
                'Access-Token' => config('services.tiktok.access_token'),
            ])->get($apiEndpoint, [
                'advertiser_id' => config('services.tiktok.advertiser_id'),
                'metrics' => json_encode(["spend","campaign_name","ad_name", "impressions","total_purchase_value",
                    "total_onsite_shopping_value","conversion_rate","clicks","offline_shopping_events_value"]),
                'data_level' => 'AUCTION_AD',
                'dimensions' => json_encode(["ad_id","stat_time_day"]),
                'service_type' => 'AUCTION',
                'report_type' => 'BASIC',
                'page_size' => 50,
                'page'  =>  1,
                'start_date' => $start_date,
                'end_date' => $end_date
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
                    $adReport['campaign_name'] = $adReportData['metrics']['campaign_name'];
                    $adReport['ad_name']=$adReportData['metrics']['ad_name'];
                    AdsReport::create($adReport);
                }
                DB::commit();
                return count($adsReport);
            } else {
                return response()->json([
                    'message' => 'Failed to fetch TikTok campaign information.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            // An exception occurred, so we roll back the transaction and return an error response
            DB::rollBack();
            Log::info($e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching TikTok campaign information.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAdsGroupsReport($start_date,$end_date)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://business-api.tiktok.com/open_api/v1.3/report/integrated/get';
            $response = Http::withHeaders([
                'Access-Token' => config('services.tiktok.access_token'),
            ])->get($apiEndpoint, [
                'advertiser_id' => config('services.tiktok.advertiser_id'),
                'metrics' => json_encode(["spend","campaign_name","adgroup_name", "impressions","total_purchase_value",
                    "total_onsite_shopping_value","conversion_rate","clicks","offline_shopping_events_value"]),
                'data_level' => 'AUCTION_ADGROUP',
                'dimensions' => json_encode(["adgroup_id","stat_time_day"]),
                'service_type' => 'AUCTION',
                'report_type' => 'BASIC',
                'page_size' => 50,
                'page'  =>  1,
                'start_date' => $start_date,
                'end_date' => $end_date
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
                    $adGroupReport['campaign_name'] = $adGroupReportData['metrics']['campaign_name'];
                    $adGroupReport['adgroup_name']=$adGroupReportData['metrics']['adgroup_name'];
                    AdsGroupsReport::create($adGroupReport);
                }
                DB::commit();
                return count($adsGroupsReport);
            } else {
                return response()->json([
                    'message' => 'Failed to fetch TikTok campaign information.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            // An exception occurred, so we roll back the transaction and return an error response
            DB::rollBack();
            Log::info($e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching TikTok campaign information.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getRangeDateMetaAndTikTok()
    {
        $start_date = '2023-01-01'; // Start date
        $end_date = '2023-12-31'; // End date

// Create DateTime objects from the start and end dates
        $start_datetime = new DateTime($start_date);
        $end_datetime = new DateTime($end_date);

// Create an interval of 1 day
        $interval = new DateInterval('P1D');

// Create a date range using the start and end dates and the interval
        $date_range = new DatePeriod($start_datetime, $interval, $end_datetime);

// Generate the list of dates in 'Y-m-d' format
        $date_list = [];
        foreach ($date_range as $date) {


            $date_list[] = $date->format('Y-m-d');
        }

// Print the list of dates
        return $date_list;

    }
    public function getDataWithRangeDate()
    {
        $date_list=$this->getRangeDateMetaAndTikTok();
        foreach ($date_list as $date)
        {
            $this->getAdsReport($date,$date);
            $this->getAdsGroupsReport($date,$date);
            $this->getCampaignsReport($date,$date);
        }

    }
//    Carbon::now()->format('Y-m-d'),Carbon::now()->format('Y-m-d')
    public function getCampaignsReport($start_date,$end_date)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://business-api.tiktok.com/open_api/v1.3/report/integrated/get';
            $response = Http::withHeaders([
                'Access-Token' => config('services.tiktok.access_token'),
            ])->get($apiEndpoint, [
                'advertiser_id' => config('services.tiktok.advertiser_id'),
                'metrics' => json_encode(["spend","campaign_name", "impressions","total_purchase_value",
                    "total_onsite_shopping_value","conversion_rate","clicks","offline_shopping_events_value"]),
                'data_level' => 'AUCTION_CAMPAIGN',
                'dimensions' => json_encode(["campaign_id","stat_time_day"]),
                'service_type' => 'AUCTION',
                'report_type' => 'BASIC',
                'page_size' => 50,
                'page'  =>  1,
                'start_date' => $start_date,
                'end_date' => $end_date
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
                    $campaignReport['campaign_name'] = $campaignReportData['metrics']['campaign_name'];
                    CampaignsReport::create($campaignReport);
                }
                DB::commit();
                return count($campaignsReport);
            } else {
                return response()->json([
                    'message' => 'Failed to fetch TikTok campaign information.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            // An exception occurred, so we roll back the transaction and return an error response
            DB::rollBack();
            Log::info($e->getMessage());
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
