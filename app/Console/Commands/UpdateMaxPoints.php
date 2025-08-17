<?php

namespace App\Console\Commands;

use App\Models\LeaderBoard;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateMaxPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard:update {--user-id= : Update max points for a specific user ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update max points for all users or a specific user based on their current subscriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($userId = $this->option('user-id')) {
            $this->updateUserMaxPoints($userId);
        } else {
            $this->updateAllUsersMaxPoints();
        }
    }

    private function updateUserMaxPoints($userId)
    {
        $user = User::find($userId);
        if (! $user) {
            $this->error("User with ID {$userId} not found.");

            return;
        }

        $leaderboard = LeaderBoard::firstOrCreate(['user_id' => $user->id]);
        $leaderboard->max_points = $user->maxPoints();
        $leaderboard->save();

        $this->info("Updated max points for user {$user->name} (ID: {$userId}): {$leaderboard->max_points}");
    }

    private function updateAllUsersMaxPoints()
    {
        $this->info('Updating max points for all users...');

        $users = User::with('division')->get();
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            if ($user->division) {
                $leaderboard = LeaderBoard::firstOrCreate(['user_id' => $user->id]);
                $leaderboard->max_points = $user->maxPoints();
                $leaderboard->save();
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Max points updated for all users.');
    }
}
