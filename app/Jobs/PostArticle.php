<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use DateTime;
use App\Models\Article;

class PostArticle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The article.
     *
     * @var App\Models\Article
     */
    protected $article;

    /**
     * Create a new job instance.
     *
     * @param App\Models\Article  $article
     * @return void
     */
    public function __construct($article)
    {
        $this->article = $article;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        config(['logging.default' => 'job']);

        // the article to be refreshed
        $article = Article::find($this->article->id);
        $this->postArticle($article);
    }

    /**
     * Post to twitter.
     *
     * @return void
     */
    public function postArticle($article)
    {
        Log::info('article ID: ' . strval($article->id) . ' priority: ' . strval($article->priority));

        if (App::environment('production')) {
            $this->postToTwitter($article);
        }

        $article->posted_at = new DateTime();
        $article->save();
    }

    /**
     * Post to twitter.
     *
     * @return void
     */
    protected function postToTwitter($article)
    {
        Log::info('post to twitter');

        $connection = new TwitterOAuth(
            config('twitter.consumer_key'),
            config('twitter.consumer_secret'),
            config('twitter.access_token'),
            config('twitter.access_token_secret')
        );
        $connection->post("statuses/update", ["status" => $article->content]);
    }
}
