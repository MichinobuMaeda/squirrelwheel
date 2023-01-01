<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;
use App\Models\Category;
use App\Models\Template;

class Initialize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:initialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert initial data.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Category::findOr(1, function () {
            return Category::create([
                'name' => 'Immediate',
                'update_only' => false,
                'priority' => 0,
                'checked_at' => new DateTime(),
            ]);
        });

        Category::findOr(2, function () {
            return Category::create([
                'name' => 'Scheduled',
                'update_only' => false,
                'priority' => 1,
                'checked_at' => new DateTime(),
            ]);
        });

        Template::where('category_id', 1)->firstOr(function () {
            Template::create([
                'category_id' => 1,
                'name' => 'Immediate',
                'body' => <<<END
%%content%%
%%link%%
END,
                'used_at' => new DateTime('2000/01/01'),
            ]);
        });

        Template::where('category_id', 2)->firstOr(function () {
            Template::create([
                'category_id' => 2,
                'name' => 'Scheduled',
                'body' => <<<END
%%content%%
%%link%%
END,
                'used_at' => new DateTime('2000/01/01'),
            ]);
        });

        return Command::SUCCESS;
    }
}
