<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;
use App\Models\Article;
use App\Jobs\PostArticle;

class DispatchPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:dispatch_post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = <<<END
Dispatch article to post.
END;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        config(['logging.default' => 'job']);
        Log::info('start: DispatchPost');

        $ts = new DateTime();

        foreach (getArticlesNotDispatched() as $article) {
            if ($article->reserved_at->format('Y-m-d\TH:i:s.vp') <= $ts->format('Y-m-d\TH:i:s.vp')) {
                Log::info('dispatch: ' . strval($article-id));
                PostArticle::dispatch($article)->onQueue('p' . strval($article->priority));

                $article->queued_at = $ts;
                $article->save();
            }
        }

        Log::info('end: DispatchPost');
        return Command::SUCCESS;
    }
}
