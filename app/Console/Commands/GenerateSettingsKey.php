<?php

namespace App\Console\Commands;

use App\SystemSetting;
use Illuminate\Console\Command;

class GenerateSettingsKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sonar:settingskey';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new key to access the settings page.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $systemSetting = SystemSetting::firstOrNew([
            'id' => 1,
        ]);

        $systemSetting->settings_key = str_random(32);
        $systemSetting->save();

        $this->info("Settings key is " . $systemSetting->settings_key);
    }
}
