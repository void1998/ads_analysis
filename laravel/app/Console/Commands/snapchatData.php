<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Models\Campaign;
use App\Models\SnapchatAdReport;
use App\Models\SnapchatAdsquadReport;
use App\Models\SnapchatCampaignReport;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class snapchatData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:snapchat-data';

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
        Log::info("snapchat data sync start");
        $accessToken = $this->getAccessToken();
        if(!$accessToken)
        {
            Log::error('snapchat task failed');
            return;
        }
        $campaignsCount = $this->getCampaigns($accessToken);
        Log::info('campaignsCount:'.$campaignsCount);
        $adsquadsCount = $this->getAdSquads($accessToken);
        Log::info('adssquadsCount:'.$adsquadsCount);
        $adsCount = $this->getAds($accessToken);
        Log::info('adsCount:'.$adsCount);
        $campaignsReportsCount = $this->getDataWithRangeDate($accessToken);
        Log::info('$campaignsReportsCount:'.$campaignsReportsCount);
//        $adsquadsReportsCount = $this->getAdsquadsReport($accessToken);
//        Log::info('$adsquadsReportsCount:'.$adsquadsReportsCount);
//        $adsReportsCount = $this->getAdsReport($accessToken);
//        Log::info('$adsReportsCount:'.$adsReportsCount);
//        Log::info("snapchat data sync end");
    }


    public function getCampaigns($accessToken)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://adsapi.snapchat.com/v1/adaccounts/bd94b527-f686-4441-a884-645f6ddb1777/campaigns';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->get($apiEndpoint, [
            ]);

            if ($response->successful()) {
                $list = $response['campaigns'];
                foreach ($list as $campaignData) {
                    $campaign = $campaignData['campaign'];
                    $campaign = $this->convertArraysToJson($campaign);
                    $carbonDate = Carbon::parse($campaign['updated_at'])->toDateTimeString();
                    $campaign['updated_at'] = $carbonDate;

                    $carbonDate = Carbon::parse($campaign['created_at'])->toDateTimeString();
                    $campaign['created_at'] = $carbonDate;

                    $carbonDate = Carbon::parse($campaign['start_time'])->toDateTimeString();
                    $campaign['start_time'] = $carbonDate;

                    $carbonDate = Carbon::now()->toDateTimeString();
                    $campaign['end_time'] = $carbonDate;
                    DB::table('snapchat_campaigns')->updateOrInsert(['id' => $campaign['id']], $campaign);
                }
                DB::commit();
                return count($list);
            } else {
                Log::error('An unexpected error occurred in get campaigns: ' );
                return response()->json([
                    'message' => 'Failed to fetch TikTok campaign information.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            // An exception occurred, so we roll back the transaction and return an error response
            DB::rollBack();
            Log::error('An unexpected error occurred in get campaigns: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching TikTok campaign information.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




    public function getAdSquads($accessToken)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://adsapi.snapchat.com/v1/adaccounts/bd94b527-f686-4441-a884-645f6ddb1777/adsquads';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->get($apiEndpoint, [
            ]);

            if ($response->successful()) {
                $list = $response['adsquads'];
                foreach ($list as $adsquadData) {
                    $adsquad = $adsquadData['adsquad'];

                    $adsquad = $this->convertArraysToJson($adsquad);
                    $carbonDate = Carbon::parse($adsquad['updated_at'])->toDateTimeString();
                    $adsquad['updated_at'] = $carbonDate;

                    $carbonDate = Carbon::parse($adsquad['created_at'])->toDateTimeString();
                    $adsquad['created_at'] = $carbonDate;

                    $carbonDate = Carbon::parse($adsquad['start_time'])->toDateTimeString();
                    $adsquad['start_time'] = $carbonDate;

                    $data = [];

                    $data['id'] = $adsquad['id'];
                    $data['name'] = $adsquad['name'];
                    $data['status'] = $adsquad['status'];
                    $data['campaign_id'] = $adsquad['campaign_id'];
                    $data['type'] = $adsquad['type'] ?? null;
                    $data['targeting'] = $adsquad['targeting'] ?? null;
                    $data['targeting_reach_status'] = $adsquad['targeting_reach_status'] ?? null;
                    $data['placement'] = $adsquad['placement'] ?? null;
                    $data['billing_event'] = $adsquad['billing_event'] ?? null;
                    $data['auto_bid'] = $adsquad['auto_bid'] ?? null;
                    $data['target_bid'] = $adsquad['target_bid'] ?? null;
                    $data['bid_strategy'] = $adsquad['bid_strategy'] ?? null;
                    $data['daily_budget_micro'] = $adsquad['daily_budget_micro'] ?? null;
                    $data['start_time'] = $adsquad['start_time'] ?? null;
                    $data['optimization_goal'] = $adsquad['optimization_goal'] ?? null;
                    $data['conversion_window'] = $adsquad['conversion_window'] ?? null;
                    $data['pixel_id'] = $adsquad['pixel_id'] ?? null;
                    $data['delivery_constraint'] = $adsquad['delivery_constraint'] ?? null;
                    $data['pacing_type'] = $adsquad['pacing_type'] ?? null;
                    $data['child_ad_type'] = $adsquad['child_ad_type'] ?? null;
                    $data['forced_view_setting'] = $adsquad['forced_view_setting'] ?? null;
                    $data['creation_state'] = $adsquad['creation_state'] ?? null;
                    $data['delivery_status'] = $adsquad['delivery_status'] ?? null;
                    $data['skadnetwork_properties'] = $adsquad['skadnetwork_properties'] ?? null;
                    $data['delivery_properties_version'] = $adsquad['delivery_properties_version'] ?? null;

                    DB::table('snapchat_adsquads')->updateOrInsert(['id' => $data['id']], $data);
                }
                DB::commit();
                return count($list);
            } else {
                Log::error('An unexpected error occurred in get adsquads: ');
                return response()->json([
                    'message' => 'Failed to fetch TikTok campaign information.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            // An exception occurred, so we roll back the transaction and return an error response
            DB::rollBack();
            Log::error('An unexpected error occurred in get adsquads: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching TikTok campaign information.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function getAds($accessToken)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://adsapi.snapchat.com/v1/adaccounts/bd94b527-f686-4441-a884-645f6ddb1777/ads';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->get($apiEndpoint, [
            ]);

            if ($response->successful()) {
                $list = $response['ads'];
                foreach ($list as $adData) {
                    $ad = $adData['ad'];
                    $ad = $this->convertArraysToJson($ad);
                    $carbonDate = Carbon::parse($ad['updated_at'])->toDateTimeString();
                    $ad['updated_at'] = $carbonDate;

                    $carbonDate = Carbon::parse($ad['created_at'])->toDateTimeString();
                    $ad['created_at'] = $carbonDate;

//                    $carbonDate = Carbon::parse($ad['start_time'])->toDateTimeString();
//                    $ad['start_time'] = $carbonDate;

                    $data['id'] = $ad['id'];
                    $data['name'] = $ad['name'];
                    $data['ad_squad_id'] = $ad['ad_squad_id'] ?? null;
                    $data['creative_id'] = $ad['creative_id'] ?? null;
                    $data['status'] = $ad['status'] ?? null;
                    $data['type'] = $ad['type'] ?? null;
                    $data['render_type'] = $ad['render_type'] ?? null;
                    $data['review_status'] = $ad['review_status'] ?? null;
                    $data['delivery_status'] = $ad['delivery_status'] ?? null;
                    DB::table('snapchat_ads')->updateOrInsert(['id' => $data['id']], $data);
                }
                DB::commit();
                return count($list);
            } else {
                Log::error('An unexpected error occurred in get ads: ');
                return response()->json([
                    'message' => 'Failed to fetch TikTok campaign information.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            // An exception occurred, so we roll back the transaction and return an error response
            DB::rollBack();
            Log::error('An unexpected error occurred in get ads: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching TikTok campaign information.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getRangeDateSnapChat()
    {
        $start_date = '2023-01-02'; // Start date
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

            $date=$date->format('Y-m-d');
            $date = new DateTime($date, new DateTimeZone('Asia/Riyadh'));
            $date_list[] = $date->format('Y-m-d\TH:i:s.vP');
        }

// Print the list of dates
        return $date_list;
    }
    public function getDataWithRangeDate($accessToken)
    {
//        $date_list=$this->getRangeDateSnapChat();
//        $prevDate='2023-01-01T00:00:00.000+03:00';
//        foreach ($date_list as $date)
//
//        {
            $date=Carbon::now('Asia/Riyadh')->startOfDay()->format('Y-m-d\TH:i:s.vP');
            $prevDate=Carbon::yesterday('Asia/Riyadh')->startOfDay()->format('Y-m-d\TH:i:s.vP');
            $this->getCampaignsReport($accessToken,$date,$prevDate);
            $this->getAdsReport($accessToken,$date,$prevDate);
            $this->getAdsquadsReport($accessToken,$date,$prevDate);
//            $prevDate=$date;
//        }

    }
//    Carbon::now('Asia/Riyadh')->startOfDay()->format('Y-m-d\TH:i:s.vP'),Carbon::yesterday('Asia/Riyadh')->startOfDay()->format('Y-m-d\TH:i:s.vP')
    public function getCampaignsReport($accessToken,$currday,$yesterday)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://adsapi.snapchat.com/v1/adaccounts/bd94b527-f686-4441-a884-645f6ddb1777/stats';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->get($apiEndpoint, [
                'breakdown'=>'campaign',
                'fields'=>
                    'conversion_purchases_value,conversion_purchases,impressions,spend,swipes,conversion_rate,conversion_add_cart,conversion_add_cart_value,conversion_page_views,conversion_page_views_value,conversion_ad_view,conversion_ad_view_value',
                'conversion_source_types'=>'total',
                'end_time'=>$currday,
                'start_time'=>$yesterday,
                'granularity'=>'DAY',
            ]);

            if ($response->successful()) {
                $campaignReports = $response['timeseries_stats'][0]['timeseries_stat']['breakdown_stats']['campaign'];
                $mil = 1000000;
                foreach ($campaignReports as $campaignReportData) {
                        $campaignReport['campaign_id'] = $campaignReportData['id'];
                        $carbonDate = Carbon::parse($campaignReportData['start_time'])->toDateTimeString();
                        $campaignReport['start_time'] = $carbonDate;
                        $carbonDate = Carbon::parse($campaignReportData['end_time'])->toDateTimeString();
                        $campaignReport['end_time'] = $carbonDate;
                        $campaignReport['impressions'] = $campaignReportData['timeseries'][0]['stats']['impressions'];
                        $campaignReport['swipes'] = $campaignReportData['timeseries'][0]['stats']['swipes'];
                        $campaignReport['spend'] = $campaignReportData['timeseries'][0]['stats']['spend']/$mil;
                        $campaignReport['conversion_purchases'] = $campaignReportData['timeseries'][0]['stats']['conversion_purchases'];
                        $campaignReport['conversion_purchases_value'] = $campaignReportData['timeseries'][0]['stats']['conversion_purchases_value']/$mil;
                        $campaignReport['conversion_add_cart'] = $campaignReportData['timeseries'][0]['stats']['conversion_add_cart'];
                        $campaignReport['conversion_add_cart_value'] = $campaignReportData['timeseries'][0]['stats']['conversion_add_cart_value']/$mil;
                        $campaignReport['conversion_page_views'] = $campaignReportData['timeseries'][0]['stats']['conversion_page_views'];
                        $campaignReport['conversion_page_views_value'] = $campaignReportData['timeseries'][0]['stats']['conversion_page_views_value']/$mil;
                        $campaignReport['conversion_ad_view'] = $campaignReportData['timeseries'][0]['stats']['conversion_ad_view'];
                        $campaignReport['conversion_ad_view_value'] = $campaignReportData['timeseries'][0]['stats']['conversion_ad_view_value']/$mil;
                        $campaignReport['conversion_rate'] = $campaignReportData['timeseries'][0]['stats']['conversion_rate'];
                        SnapchatCampaignReport::create($campaignReport);
                }
                DB::commit();
                return count($campaignReport);
            } else {
                Log::error('An unexpected error occurred in get snapchat campaign report: ');
                return response()->json([
                    'message' => 'Failed to fetch Snapchat campaign report information.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            // An exception occurred, so we roll back the transaction and return an error response
            DB::rollBack();
            Log::error('An unexpected error occurred in get snapchat campaign report: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching snapchat campaign report information.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getAdsquadsReport($accessToken,$currday,$yesterday)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://adsapi.snapchat.com/v1/adaccounts/bd94b527-f686-4441-a884-645f6ddb1777/stats';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->get($apiEndpoint, [
                'breakdown'=>'adsquad',
                'fields'=>
                    'conversion_purchases_value,conversion_purchases,impressions,spend,swipes,conversion_rate,conversion_add_cart,conversion_add_cart_value,conversion_page_views,conversion_page_views_value,conversion_ad_view,conversion_ad_view_value',
                'conversion_source_types'=>'total',
                'end_time'=>$currday,
                'start_time'=>$yesterday,
                'granularity'=>'DAY',
            ]);

            if ($response->successful()) {
                $adsquadReports = $response['timeseries_stats'][0]['timeseries_stat']['breakdown_stats']['adsquad'];
                $mil = 1000000;
                foreach ($adsquadReports as $adsquadReportData) {
                    $adsquadReport['adsquad_id'] = $adsquadReportData['id'];
                    $carbonDate = Carbon::parse($adsquadReportData['start_time'])->toDateTimeString();
                    $adsquadReport['start_time'] = $carbonDate;
                    $carbonDate = Carbon::parse($adsquadReportData['end_time'])->toDateTimeString();
                    $adsquadReport['end_time'] = $carbonDate;
                    $adsquadReport['impressions'] = $adsquadReportData['timeseries'][0]['stats']['impressions'];
                    $adsquadReport['swipes'] = $adsquadReportData['timeseries'][0]['stats']['swipes'];
                    $adsquadReport['spend'] = $adsquadReportData['timeseries'][0]['stats']['spend']/$mil;
                    $adsquadReport['conversion_purchases'] = $adsquadReportData['timeseries'][0]['stats']['conversion_purchases'];
                    $adsquadReport['conversion_purchases_value'] = $adsquadReportData['timeseries'][0]['stats']['conversion_purchases_value']/$mil;
                    $adsquadReport['conversion_add_cart'] = $adsquadReportData['timeseries'][0]['stats']['conversion_add_cart'];
                    $adsquadReport['conversion_add_cart_value'] = $adsquadReportData['timeseries'][0]['stats']['conversion_add_cart_value']/$mil;
                    $adsquadReport['conversion_page_views'] = $adsquadReportData['timeseries'][0]['stats']['conversion_page_views'];
                    $adsquadReport['conversion_page_views_value'] = $adsquadReportData['timeseries'][0]['stats']['conversion_page_views_value']/$mil;
                    $adsquadReport['conversion_ad_view'] = $adsquadReportData['timeseries'][0]['stats']['conversion_ad_view'];
                    $adsquadReport['conversion_ad_view_value'] = $adsquadReportData['timeseries'][0]['stats']['conversion_ad_view_value']/$mil;
                    $adsquadReport['conversion_rate'] = $adsquadReportData['timeseries'][0]['stats']['conversion_rate'];
                    SnapchatAdsquadReport::create($adsquadReport);
                }
                DB::commit();
                return count($adsquadReports);
            } else {
                Log::error('An unexpected error occurred in get $adsquadReports: ');
                return response()->json([
                    'message' => 'Failed to fetch Snapchat campaign report information.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            // An exception occurred, so we roll back the transaction and return an error response
            DB::rollBack();
            Log::error('An unexpected error occurred in get $adsquadReports: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching snapchat campaign report information.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getAdsReport($accessToken,$currday,$yesterday)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://adsapi.snapchat.com/v1/adaccounts/bd94b527-f686-4441-a884-645f6ddb1777/stats';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->get($apiEndpoint, [
                'breakdown'=>'ad',
                'fields'=>
                    'conversion_purchases_value,conversion_purchases,impressions,spend,swipes,conversion_rate,conversion_add_cart,conversion_add_cart_value,conversion_page_views,conversion_page_views_value,conversion_ad_view,conversion_ad_view_value',
                'conversion_source_types'=>'total',
                'end_time'=>$currday,
                'start_time'=>$yesterday,
                'granularity'=>'DAY',
            ]);

            if ($response->successful()) {
                $adReports = $response['timeseries_stats'][0]['timeseries_stat']['breakdown_stats']['ad'];
                $mil = 1000000;
                foreach ($adReports as $adReportData) {
                    $adReport['ad_id'] = $adReportData['id'];
                    $carbonDate = Carbon::parse($adReportData['start_time'])->toDateTimeString();
                    $adReport['start_time'] = $carbonDate;
                    $carbonDate = Carbon::parse($adReportData['end_time'])->toDateTimeString();
                    $adReport['end_time'] = $carbonDate;
                    $adReport['impressions'] = $adReportData['timeseries'][0]['stats']['impressions'];
                    $adReport['swipes'] = $adReportData['timeseries'][0]['stats']['swipes'];
                    $adReport['spend'] = $adReportData['timeseries'][0]['stats']['spend']/$mil;
                    $adReport['conversion_purchases'] = $adReportData['timeseries'][0]['stats']['conversion_purchases'];
                    $adReport['conversion_purchases_value'] = $adReportData['timeseries'][0]['stats']['conversion_purchases_value']/$mil;
                    $adReport['conversion_add_cart'] = $adReportData['timeseries'][0]['stats']['conversion_add_cart'];
                    $adReport['conversion_add_cart_value'] = $adReportData['timeseries'][0]['stats']['conversion_add_cart_value']/$mil;
                    $adReport['conversion_page_views'] = $adReportData['timeseries'][0]['stats']['conversion_page_views'];
                    $adReport['conversion_page_views_value'] = $adReportData['timeseries'][0]['stats']['conversion_page_views_value']/$mil;
                    $adReport['conversion_ad_view'] = $adReportData['timeseries'][0]['stats']['conversion_ad_view'];
                    $adReport['conversion_ad_view_value'] = $adReportData['timeseries'][0]['stats']['conversion_ad_view_value']/$mil;
                    $adReport['conversion_rate'] = $adReportData['timeseries'][0]['stats']['conversion_rate'];
                    SnapchatAdReport::create($adReport);
                }
                DB::commit();
                return count($adReports);
            } else {
                Log::error('An unexpected error occurred in get $adReport: ');
                return response()->json([
                    'message' => 'Failed to fetch Snapchat campaign report information.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            // An exception occurred, so we roll back the transaction and return an error response
            DB::rollBack();
            Log::error('An unexpected error occurred in get $adReport: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching snapchat campaign report information.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAccessToken()
    {
        try {
            // Fetch campaign information from the TikTok API
            $refresh_token = config('services.snapchat.refresh_token');
            $client_id = config('services.snapchat.client_id');
            $client_secret = config('services.snapchat.client_secret');
            $apiEndpoint = 'https://accounts.snapchat.com/login/oauth2/access_token';

            $response = Http::withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post($apiEndpoint, [
                'refresh_token' => $refresh_token,
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'grant_type' => 'refresh_token',
            ]);
            if ($response->successful()) {
                $response = $response->json();
                return $response['access_token'];
            } else {
                $statusCode = $response->status();
                $errorMessage = 'Failed to get access token.';

                // Log the error message
                Log::error($errorMessage .' '.$response->json()['error'], ['status_code' => $statusCode]);

                return 0;
            }
        } catch (\Exception $e) {
            // Log any unexpected exceptions
            Log::error('An unexpected error occurred in get access token: ' . $e->getMessage());

            // Return a generic error response
            return 0;
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
