<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\PostArticle;
use App\Repositories\ArticleRepository;

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
    protected $description = 'Dispatch article to post.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ArticleRepository $articles)
    {
        config(['logging.default' => 'job']);
        Log::info('start: DispatchPost');

        $ts = new DateTime();

        foreach ($articles->listToBeQueued($ts) as $article) {
            Log::info('dispatch: ' . strval($article->id));
            PostArticle::dispatch($article)
                ->onQueue('p' . strval($article->priority));
            $article->fill(['queued_at' => $ts])->save();
        }

        Log::info('end: DispatchPost');
        return Command::SUCCESS;
    }
}
