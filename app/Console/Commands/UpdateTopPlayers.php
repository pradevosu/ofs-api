<?php

namespace App\Console\Commands;

use App\Services\OsuApiService;
use Illuminate\Console\Command;

class UpdateTopPlayers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-top-players';

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
        $this->info('Updating top players...');

        try {
            $service = app(OsuApiService::class);
            $ranking = $service->getRanking();

            if ($ranking) {
                $data = print_r($ranking, true);
                $this->info("{$data}");
            } else {
                $this->error('Failed to retrieve ranking data.');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }

        $this->info('Top players updated successfully.');
    }
}
