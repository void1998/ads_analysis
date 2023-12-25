<?php

namespace App\Console\Commands;

use App\Models\AudienceNetworkAdSetReport;
use App\Models\AudienceNetworkCampaignReport;
use App\Models\AudienceNetwrokAdReport;
use App\Models\CampaignsReport;
use App\Models\FacebookAdReport;
use App\Models\FacebookAdSetReport;
use App\Models\FacebookCampaignReport;
use App\Models\InstagramAdReport;
use App\Models\InstagramAdSetReport;
use App\Models\InstagramCampaignReport;
use App\Models\MessengerAdReport;
use App\Models\MessengerAdSetReport;
use App\Models\MessengerCampaignReport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class metaData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:meta-data';

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
        Log::info("meta data sync start");
        $campaignsReportsCount = $this->getCampaignsReport();
        Log::info('$campaignsReportsCount:'.$campaignsReportsCount);
        $adSetsReportsCount = $this->getAdSetsReport();
        Log::info('$adSetsReportsCount:'.$adSetsReportsCount);
        $adsReportsCount = $this->getAdsReport();
        Log::info('$adsReportsCount:'.$adsReportsCount);
        Log::info("meta data sync end");
        $this->info('end');
        $this->info($campaignsReportsCount);
        $this->info($adSetsReportsCount);
        $this->info($adsReportsCount);

    }



    public function getCampaignsReport()
    {
        try {

            $apiEndpoint = 'https://graph.facebook.com/v18.0/act_825886441839608/insights';
            $nextPage = '';
            $stop = false;
            do{
                DB::beginTransaction();
                $response = Http::withHeaders([
//                'Access-Token' => config('services.tiktok.access_token'),
                ])->get($apiEndpoint, [
                    'access_token' => config('services.meta.access_token'),
                    'fields' => json_encode(["impressions","campaign_id","campaign_name","clicks","purchase_roas","spend","conversion_values","conversions"]),
                    'level' => 'campaign',
                    'time_range' => json_encode(["since"=>Carbon::now()->format('Y-m-d'),
                        "until"=>Carbon::now()->format('Y-m-d')]),
                    'time_increment' => '1',
                    'breakdowns' => 'publisher_platform',
                    'page_size' => 50,
                    'after' => $nextPage
                ]);
                if ($response->successful()) {
                    $campaignsReport = $response['data'];
                    if(count($campaignsReport) <= 0)
                    {
                        $stop = true;
                    }else
                    {
                        $nextPage = $response['paging']['cursors']['after'];
                    }
                    foreach ($campaignsReport as $campaignReportData) {
                        $campaignReport = [];
                        $campaignReport['impressions'] = $campaignReportData['impressions'];
                        $campaignReport['campaign_id'] = $campaignReportData['campaign_id'];
                        $campaignReport['campaign_name'] = $campaignReportData['campaign_name'];
                        $campaignReport['clicks'] = $campaignReportData['clicks'];
                        $campaignReport['spend'] = $campaignReportData['spend'];
                        $campaignReport['date_start'] = $campaignReportData['date_start'];
                        $campaignReport['date_stop'] = $campaignReportData['date_stop'];
                        $campaignReport['publisher_platform'] = $campaignReportData['publisher_platform'];
                        $campaignReport['purchase_roas'] = isset($campaignReportData['purchase_roas']) ? json_encode($campaignReportData['purchase_roas']) : null;

                        if($campaignReport['publisher_platform'] == 'facebook')
                        {
                            FacebookCampaignReport::create($campaignReport);
                        }else if($campaignReport['publisher_platform'] == 'instagram')
                        {
                            InstagramCampaignReport::create($campaignReport);
                        }
                        else if($campaignReport['publisher_platform'] == 'messenger')
                        {
                            MessengerCampaignReport::create($campaignReport);
                        }
                        else if($campaignReport['publisher_platform'] == 'audience_network')
                        {
                            AudienceNetworkCampaignReport::create($campaignReport);
                        }

                    }
                    DB::commit();
                    Log::info(count($campaignsReport));
                } else {
                    Log::info('Failed to fetch meta campaign information.');
                    return response()->json([
                        'message' => 'Failed to fetch meta campaign information.',
                    ], $response->status());
                    break;
                }

            }
            while(!$stop);
        } catch (\Exception $e) {
            // An exception occurred, so we roll back the transaction and return an error response
            DB::rollBack();
            Log::info($e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching meta campaign information.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getAdsReport()
    {
        try {

            $apiEndpoint = 'https://graph.facebook.com/v18.0/act_825886441839608/insights';
            $stop = false;
            $nextPage = '';
            do{
                DB::beginTransaction();
                $response = Http::withHeaders([
//                'Access-Token' => config('services.tiktok.access_token'),
                ])->get($apiEndpoint, [
                    'access_token' => config('services.meta.access_token'),
                    'fields' => json_encode(["impressions","campaign_id","adset_id","ad_id","campaign_name","clicks","purchase_roas","spend","conversion_values","conversions"]),
                    'level' => 'ad',
                    'time_range' => json_encode(["since"=>Carbon::now()->format('Y-m-d'),
                        "until"=>Carbon::now()->format('Y-m-d')]),
                    'time_increment' => '1',
                    'breakdowns' => 'publisher_platform',
                    'page_size' => 50,
                    'after' =>  $nextPage
                ]);
                if ($response->successful()) {
                    $adsReport = $response['data'];
                    if(count($adsReport) <= 0)
                    {
                        $stop = true;
                    }else
                    {
                        $nextPage = $response['paging']['cursors']['after'];
                    }
                    foreach ($adsReport as $adReportData) {
                        $adReport = [];
                        $adReport['impressions'] = $adReportData['impressions'];
                        $adReport['ad_id'] = $adReportData['ad_id'];
                        $adReport['adset_id'] = $adReportData['adset_id'];
                        $adReport['campaign_id'] = $adReportData['campaign_id'];
                        $adReport['campaign_name'] = $adReportData['campaign_name'];
                        $adReport['clicks'] = $adReportData['clicks'];
                        $adReport['spend'] = $adReportData['spend'];
                        $adReport['date_start'] = $adReportData['date_start'];
                        $adReport['date_stop'] = $adReportData['date_stop'];
                        $adReport['publisher_platform'] = $adReportData['publisher_platform'];
                        $adReport['purchase_roas'] = isset($adReportData['purchase_roas']) ? json_encode($adReportData['purchase_roas']) : null;

                        if($adReport['publisher_platform'] == 'facebook')
                        {
                            FacebookAdReport::create($adReport);
                        }else if($adReport['publisher_platform'] == 'instagram')
                        {
                            InstagramAdReport::create($adReport);
                        }
                        else if($adReport['publisher_platform'] == 'messenger')
                        {
                            MessengerAdReport::create($adReport);
                        }
                        else if($adReport['publisher_platform'] == 'audience_network')
                        {
                            AudienceNetwrokAdReport::create($adReport);
                        }

                    }
                    DB::commit();
                    Log::info(count($adsReport));
                } else {
                    Log::info('Failed to fetch meta ad information.');
                    return response()->json([
                        'message' => 'Failed to fetch meta ad information.',
                    ], $response->status());
                    break;
                }
            }
            while(!$stop);

        } catch (\Exception $e) {
            // An exception occurred, so we roll back the transaction and return an error response
            DB::rollBack();
            Log::info($e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching meta ad information.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getAdSetsReport()
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://graph.facebook.com/v18.0/act_825886441839608/insights';
            $stop = false;
            $nextPage = '';
            do{
                $response = Http::withHeaders([
//                'Access-Token' => config('services.tiktok.access_token'),
                ])->get($apiEndpoint, [
                    'access_token' => config('services.meta.access_token'),
                    'fields' => json_encode(["impressions","campaign_id","adset_id","campaign_name","clicks","purchase_roas","spend","conversion_values","conversions"]),
                    'level' => 'adset',
                    'time_range' => json_encode(["since"=>Carbon::now()->format('Y-m-d'),
                        "until"=>Carbon::now()->format('Y-m-d')]),
                    'time_increment' => '1',
                    'breakdowns' => 'publisher_platform',
                    'page_size' => 50,
                    'after' => $nextPage
                ]);
                if ($response->successful()) {
                    $adsetsReport = $response['data'];
                    if(count($adsetsReport) <= 0)
                    {
                        $stop = true;
                    }else
                    {
                        $nextPage = $response['paging']['cursors']['after'];
                    }
                    foreach ($adsetsReport as $adsetReportData) {
                        $adsetReport = [];
                        $adsetReport['impressions'] = $adsetReportData['impressions'];
                        $adsetReport['adset_id'] = $adsetReportData['adset_id'];
                        $adsetReport['campaign_id'] = $adsetReportData['campaign_id'];
                        $adsetReport['campaign_name'] = $adsetReportData['campaign_name'];
                        $adsetReport['clicks'] = $adsetReportData['clicks'];
                        $adsetReport['spend'] = $adsetReportData['spend'];
                        $adsetReport['date_start'] = $adsetReportData['date_start'];
                        $adsetReport['date_stop'] = $adsetReportData['date_stop'];
                        $adsetReport['publisher_platform'] = $adsetReportData['publisher_platform'];
                        $adsetReport['purchase_roas'] = isset($adsetReportData['purchase_roas']) ? json_encode($adsetReportData['purchase_roas']) : null;

                        if($adsetReport['publisher_platform'] == 'facebook')
                        {
                            FacebookAdSetReport::create($adsetReport);
                        }else if($adsetReport['publisher_platform'] == 'instagram')
                        {
                            InstagramAdSetReport::create($adsetReport);
                        }
                        else if($adsetReport['publisher_platform'] == 'messenger')
                        {
                            MessengerAdSetReport::create($adsetReport);
                        }
                        else if($adsetReport['publisher_platform'] == 'audience_network')
                        {
                            AudienceNetworkAdSetReport::create($adsetReport);
                        }

                    }
                    DB::commit();
                    Log::info(count($adsetsReport));
                } else {
                    Log::info('Failed to fetch meta adset information.');
                    return response()->json([
                        'message' => 'Failed to fetch meta adset information.',
                    ], $response->status());
                    break;
                }
            }while(!$stop);

        } catch (\Exception $e) {
            // An exception occurred, so we roll back the transaction and return an error response
            DB::rollBack();
            Log::info($e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching meta ad set information.',
                'error' => $e->getMessage(),
            ], 500);
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
