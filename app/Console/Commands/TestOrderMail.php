<?php

namespace App\Console\Commands;

use App\Mail\NewOrderMail;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestOrderMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:order-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test order mail with approval button';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $order = Order::first();
        if (!$order) {
            $this->error('No orders found');
            return 1;
        }

        $token = 'test-token-' . time();
        $email = 'test@example.com';

        $this->info("Sending test email to {$email}");
        Mail::to($email)->send(new NewOrderMail($order, $token));
        $this->info('Email sent!');

        return 0;
    }
}
