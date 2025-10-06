<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\LeaderboardBadgeController;

class AwardMonthlySchoolBadges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard:award-monthly-school-badges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Award monthly school leaderboard badges to top 10 students';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Awarding monthly school leaderboard badges...');

        $controller = new LeaderboardBadgeController();
        $result = $controller->awardMonthlySchoolBadges();

        if ($result['success']) {
            $this->info("Successfully awarded {$result['badges_awarded']} badges!");
        } else {
            $this->error("Failed to award badges: {$result['error']}");
            return 1;
        }

        return 0;
    }
}
