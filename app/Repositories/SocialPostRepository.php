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
        $targets = config('sqwh.post_target');
        Log::info(
            'targets: ' . join(',', $targets) .
            ' priority: ' . strval($article->priority) .
            ' article ID: ' . strval($article->id)
        );

        if (config('app.env') === 'production') {
            if (in_array('tw', $targets , true)) {
                $this->postToTwitter($article);
            }
            if (in_array('mstdn', $targets , true)) {
                $this->postToMastodon($article);
            }
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

    /**
     * Post the article to mastodon.
     *
     * @param Article  $article
     * @return void
     */
    protected function postToMastodon(Article $article)
    {
        Log::info('post to mastodon');

        // TODO:
    }
}
