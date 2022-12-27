<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
        $id_manual = '';

        if (!Category::findOr($id_manual, function () { return false; }))
        {
            $category = new Category;
            $category->id = $id_manual;
            $category->name = 'Manual';
            $category->update_only = false;
            $category->priority = 0;
            $category->save();
        }

        if (!Template::where('category_id', $id_manual)
            ->firstOr(function () { return false; }))
        {
            $template = new Template;
            $template->category_id = '';
            $template->name = 'Manual';
            $template->body = <<<END
%%content%%

%%link%%
END;
            $template->save();
        }

        return Command::SUCCESS;
    }
}
