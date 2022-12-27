<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReadFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:read_feed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = <<<END
Read the site's feed and add article posts to the queue.
END;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return Command::SUCCESS;
    }
}
