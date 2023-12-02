<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Models\Campaign;
use App\Models\SnapchatCampaignReport;
use Carbon\Carbon;
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
        }
        $campaignsCount = $this->getCampaigns($accessToken);
        Log::info('campaignsCount:'.$campaignsCount);
        $adsquadsCount = $this->getAdSquads($accessToken);
        Log::info('adssquadsCount:'.$adsquadsCount);
        $adsCount = $this->getAds($accessToken);
        Log::info('adsCount:'.$adsCount);
        $campaignsReportsCount = $this->getCampaignsReport($accessToken);
        Log::info('$campaignsReportsCount:'.$campaignsReportsCount);
        $adsquadsReportsCount = $this->getAdsquadsReport($accessToken);
        Log::info('$adsquadsReportsCount:'.$adsquadsReportsCount);
        $adsReportsCount = $this->getAdsReport($accessToken);
        Log::info('$adsReportsCount:'.$adsReportsCount);
        Log::info("snapchat data sync start");
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
                    DB::table('snapchat_campaigns')->updateOrInsert(['id' => $campaign['id'], $campaign]);
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
                    DB::table('snapchat_adsquads')->updateOrInsert(['id' => $adsquad['id'], $adsquad]);
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
                    DB::table('snapchat_ads')->updateOrInsert(['id' => $adData['id'], $adData]);
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


    public function getCampaignsReport($accessToken)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://adsapi.snapchat.com/v1/adaccounts/bd94b527-f686-4441-a884-645f6ddb1777/stats';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->get($apiEndpoint, [
                'breakdown'=>'campaign',
                'fields'=>
                    'conversion_purchases_value,
                    conversion_purchases,impressions,
                    spend,swipes,conversion_rate,
                    conversion_add_cart,
                    conversion_add_cart_value,
                    conversion_page_views,
                    conversion_page_views_value,
                    conversion_ad_view,
                    conversion_ad_view_value',
                'conversion_source_types'=>'total',
                'start_time'=>Carbon::now()->format('Y-m-d H:i:s'),
                'end_time'=>Carbon::yesterday()->format('Y-m-d H:i:s'),
                'granularity'=>'DAY',
            ]);

            if ($response->successful()) {
                $campaignReports = $response['timeseries_stats']['timeseries_stat']['breakdown_stats']['campaign'];
                $mil = 1000000;
                foreach ($campaignReports as $campaignReportData) {
                        $campaignReport['campaign_id'] = $campaignReportData['id'];
                        $campaignReport['start_time'] = $campaignReportData['start_time'];
                        $campaignReport['end_time'] = $campaignReportData['end_time'];
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


    public function getAdsquadsReport($accessToken)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://adsapi.snapchat.com/v1/adaccounts/bd94b527-f686-4441-a884-645f6ddb1777/stats';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->get($apiEndpoint, [
                'breakdown'=>'adsquad',
                'fields'=>
                    'conversion_purchases_value,
                    conversion_purchases,impressions,
                    spend,swipes,conversion_rate,
                    conversion_add_cart,
                    conversion_add_cart_value,
                    conversion_page_views,
                    conversion_page_views_value,
                    conversion_ad_view,
                    conversion_ad_view_value',
                'conversion_source_types'=>'total',
                'start_time'=>Carbon::now()->format('Y-m-d H:i:s'),
                'end_time'=>Carbon::yesterday()->format('Y-m-d H:i:s'),
                'granularity'=>'DAY',
            ]);

            if ($response->successful()) {
                $adsquadReports = $response['timeseries_stats']['timeseries_stat']['breakdown_stats']['adsquad'];
                $mil = 1000000;
                foreach ($adsquadReports as $adsquadReportData) {
                    $adsquadReport['adsquad_id'] = $adsquadReportData['id'];
                    $adsquadReport['start_time'] = $adsquadReportData['start_time'];
                    $adsquadReport['end_time'] = $adsquadReportData['end_time'];
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
                    SnapchatCampaignReport::create($adsquadReport);
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


    public function getAdsReport($accessToken)
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://adsapi.snapchat.com/v1/adaccounts/bd94b527-f686-4441-a884-645f6ddb1777/stats';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->get($apiEndpoint, [
                'breakdown'=>'ad',
                'fields'=>
                    'conversion_purchases_value,
                    conversion_purchases,impressions,
                    spend,swipes,conversion_rate,
                    conversion_add_cart,
                    conversion_add_cart_value,
                    conversion_page_views,
                    conversion_page_views_value,
                    conversion_ad_view,
                    conversion_ad_view_value',
                'conversion_source_types'=>'total',
                'start_time'=>Carbon::now()->format('Y-m-d H:i:s'),
                'end_time'=>Carbon::yesterday()->format('Y-m-d H:i:s'),
                'granularity'=>'DAY',
            ]);

            if ($response->successful()) {
                $adReports = $response['timeseries_stats']['timeseries_stat']['breakdown_stats']['ad'];
                $mil = 1000000;
                foreach ($adReports as $adReportData) {
                    $adReport['ad_id'] = $adReportData['id'];
                    $adReport['start_time'] = $adReportData['start_time'];
                    $adReport['end_time'] = $adReportData['end_time'];
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
                    SnapchatCampaignReport::create($adReport);
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

            $response = Http::post($apiEndpoint, [
                'refresh_token' => $refresh_token,
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'grant_type' => 'refresh_token',
            ]);
            dd($response);
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
}
