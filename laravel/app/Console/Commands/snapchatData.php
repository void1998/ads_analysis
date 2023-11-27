<?php

namespace App\Console\Commands;

use App\Models\Campaign;
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

    public function getAccessToken()
    {
        try {
            // Fetch campaign information from the TikTok API
            $refresh_token = config('services.snapchat.refresh_token');
            $client_id = config('services.snapchat.client_id');
            $client_secret = config('services.snapchat.client_secret');
            $apiEndpoint = 'https://accounts.snapchat.com/login/oauth2/access_token';

            $response = Http::withHeaders([
                'refresh_token' => $refresh_token,
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'grant_type' => 'refresh_token',
            ])->get($apiEndpoint);

            if ($response->successful()) {
                $response = $response->json();
                return $response['access_token'];
            } else {
                $statusCode = $response->status();
                $errorMessage = 'Failed to get access token.';

                // Log the error message
                Log::error($errorMessage, ['status_code' => $statusCode]);

                return $errorMessage;
            }
        } catch (\Exception $e) {
            // Log any unexpected exceptions
            Log::error('An unexpected error occurred in get access token: ' . $e->getMessage());

            // Return a generic error response
            return $e->getMessage();
        }
    }
}
