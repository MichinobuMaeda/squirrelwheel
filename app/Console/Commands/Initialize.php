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
        Category::findOr('@immediate', function () {
            Category::create([
                'id' => '@immediate',
                'name' => 'Immediate',
                'update_only' => false,
                'priority' => 0,
                'checked_at' => new DateTime(),
            ])->save();
        });

        Category::findOr('@later', function () {
            Category::create([
                'id' => '@later',
                'name' => 'Later',
                'update_only' => false,
                'priority' => 1,
                'checked_at' => new DateTime(),
            ])->save();
        });

        Template::where('category_id', '@immediate')->firstOr(function () {
            Template::create([
                'category_id' => '@immediate',
                'name' => 'Immediate',
                'body' => <<<END
%%content%%
%%link%%
END,
                'used_at' => new DateTime('2000/01/01'),
            ])->save();
        });

        Template::where('category_id', '@later')->firstOr(function () {
            Template::create([
                'category_id' => '@later',
                'name' => 'Later',
                'body' => <<<END
%%content%%
%%link%%
END,
                'used_at' => new DateTime('2000/01/01'),
            ])->save();
        });

        return Command::SUCCESS;
    }
}
