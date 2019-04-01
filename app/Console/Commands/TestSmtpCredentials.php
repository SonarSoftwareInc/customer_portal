<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class TestSmtpCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sonar:test:smtp {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the SMTP credentials from your .env file.';

    /**
     * Create a new command instance.
     *
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
        $email = $this->argument('email');
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->error($email . " is not a valid email address.");
            return;
        }

        try {
            Mail::send('emails.test', [], function ($m) use ($email) {
                $m->from(config("customer_portal.from_address"), config("customer_portal.from_name"))
                    ->to($email, $email)
                    ->subject('Sonar customer portal test email!');
            });

            $this->info("Test email to $email sent.");
        } catch (Exception $e) {
            $this->error("Test email failed with the following: " . $e->getMessage());
        }
    }
}
