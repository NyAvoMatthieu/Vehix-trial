<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         Mail::raw('Ceci est un test.', function ($message) {
            $message->to('hasinaandritina538@gmail.com');
            $message->subject('Test d\'email');
        });

        $this->info('Email envoyé !');
    }
}
