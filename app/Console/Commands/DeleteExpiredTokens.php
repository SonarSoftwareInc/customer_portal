<?php

namespace App\Console\Commands;

use App\CreationToken;
use App\GoCardlessToken;
use App\PasswordReset;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sonar:deleteexpiredtokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired tokens from the SQLite database';

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
        $deleteTokensOlderThan = Carbon::now("UTC")->subHours(24)->toDateTimeString();
        CreationToken::where('updated_at', '<=', $deleteTokensOlderThan)->delete();
        PasswordReset::where('updated_at', '<=', $deleteTokensOlderThan)->delete();
        GoCardlessToken::where('updated_at','<=',$deleteTokensOlderThan)->delete();
        $this->info("Tokens deleted.");
    }
}
