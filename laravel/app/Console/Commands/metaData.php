<?php

namespace App\Console\Commands;

use App\Models\CampaignsReport;
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
        //
    }



    public function getCampaignsReport()
    {
        try {
            DB::beginTransaction();
            $apiEndpoint = 'https://graph.facebook.com/v18.0/act_825886441839608/insights';
            $response = Http::withHeaders([
//                'Access-Token' => config('services.tiktok.access_token'),
            ])->get($apiEndpoint, [
                'fields' => json_encode(["impressions","campaign_id","campaign_name","clicks","purchase_roas","spend","conversion_values","conversions"]),
                'data_level' => 'AUCTION_CAMPAIGN',
                'dimensions' => json_encode(["campaign_id","stat_time_day"]),
                'service_type' => 'AUCTION',
                'report_type' => 'BASIC',
                'page_size' => 50,
                'page'  =>  1,
                'start_date' => Carbon::now()->format('Y-m-d'),
                'end_date' => Carbon::now()->format('Y-m-d')
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
