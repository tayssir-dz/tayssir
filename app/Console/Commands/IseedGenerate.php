<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class IseedGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iseed:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate seeders for app content tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $this->call('iseed', [
        //     'tables' => 'users,roles,permissions,model_has_permissions,model_has_roles,role_has_permissions,chapter_question,chapter_subscription,chapter_unit,chapters,discount_subscription,discounts,division_material,divisions,material_unit,materials,questions,subscription_cards,subscription_unit,subscriptions,units,user_answers',
        //     '--clean' => true,
        //     '--force' => true,
        //     '--classnameprefix' => 'Iseed', // all generated classes will share this prefix
        // ]);
        $this->call('iseed', [
            'tables' => 'wilayas,communes',
            '--clean' => true,
            '--force' => true,
            '--classnameprefix' => 'Iseed', // all generated classes will share this prefix
        ]);
    }
}
