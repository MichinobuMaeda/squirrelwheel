<?php

namespace App\Repositories;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Log;
use App\Models\Article;

class SocialPostRepository
{
    /**
     * Post the article.
     *
     * @param Article  $article
     * @return void
     */
    public function post(Article $article)
    {
        Log::info('article ID: ' . strval($article->id) . ' priority: ' . strval($article->priority));

        if (config('app.env') === 'production') {
            $this->postToTwitter($article);
        }
    }

    /**
     * Post the article to twitter.
     *
     * @param Article  $article
     * @return void
     */
    protected function postToTwitter(Article $article)
    {
        Log::info('post to twitter');

        $connection = new TwitterOAuth(
            config('sqwh.tw.consumer_key'),
            config('sqwh.tw.consumer_secret'),
            config('sqwh.tw.access_token'),
            config('sqwh.tw.access_token_secret')
        );
        $connection->post("statuses/update", ["status" => $article->content]);
    }
}
