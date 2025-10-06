<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\LeaderboardBadgeController;

class AwardWeeklySectionBadges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard:award-weekly-section-badges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Award weekly section leaderboard badges to top 10 students';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Awarding weekly section leaderboard badges...');

        $controller = new LeaderboardBadgeController();
        $result = $controller->awardWeeklySectionBadges();

        if ($result['success']) {
            $this->info("Successfully awarded {$result['badges_awarded']} badges!");
        } else {
            $this->error("Failed to award badges: {$result['error']}");
            return 1;
        }

        return 0;
    }
}
