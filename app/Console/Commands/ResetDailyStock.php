<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Produk;

class ResetDailyStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:reset-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset daily stock counters (moves closing stock to opening stock)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting daily stock reset...');

        $products = Produk::all();
        $count = 0;

        foreach ($products as $product) {
            $product->resetDailyStock();
            $count++;
        }

        $this->info("Successfully reset stock for {$count} products.");
        
        return Command::SUCCESS;
    }
}