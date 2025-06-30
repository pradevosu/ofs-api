<?php

namespace App\Services;

use App\Models\Player;
use App\Models\PlayerStatistics;
use Illuminate\Support\Facades\Http;
class OsuApiService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = 'https://osu.ppy.sh/api/v2/';
        $this->token = $this->getToken();
    }

    protected function getToken() : string
    {
        $response = Http::asForm()->post(
            'https://osu.ppy.sh/oauth/token',
            [
                'client_id' => env('OSU_CLIENT_ID'),
                'client_secret' => env('OSU_CLIENT_SECRET'),
                'grant_type' => 'client_credentials',
                'scope' => 'public'
            ]
        );

        return $response->json()['access_token'];
    }

    public function getRanking() : bool
    {
        $response = Http::withToken($this->token)->get(
            $this->baseUrl . 'rankings/osu/performance',
            [
                'country' => 'FR',
            ]
        );

        if ($response->successful()) {
            $playersData = [];
            $playerStatsData = [];

            foreach ($response->json()['ranking'] as $ranking) {
                $playersData[] = [
                    'id' => $ranking['user']['id'],
                    'username' => $ranking['user']['username'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ];

                $playerStatsData[] = [
                    'player_id' => $ranking['user']['id'],
                    'global_rank' => $ranking['global_rank'],
                    'country_rank' => $ranking['country_rank'],
                    'pp' => $ranking['pp'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ];
            }

            Player::upsert($playersData, ['id'], ['username', 'updated_at']);
            PlayerStatistics::upsert($playerStatsData, ['player_id'], ['global_rank', 'country_rank', 'pp', 'updated_at']);
            return true;
        }
        return false;
    }
}
