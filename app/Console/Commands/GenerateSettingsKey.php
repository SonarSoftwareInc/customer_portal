<?php

namespace App\Console\Commands;

use App\SystemSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

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
     */
    public function handle(): void
    {
        $systemSetting = SystemSetting::firstOrNew([
            'id' => 1,
        ]);

        $systemSetting->settings_key = Str::random(32);
        $systemSetting->save();

        $this->info('Settings key is '.$systemSetting->settings_key);
    }
}
