<?php

namespace App\Repositories;

use DateTime;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Log;
use App\Models\Article;
use App\Repositories\MastodonApiRepository;

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
        Log::info(
            'targets: ' . join(' ', $article->post_targets) .
                ' priority: ' . strval($article->priority) .
                ' article ID: ' . strval($article->id)
        );

        if (config('app.env') === 'production') {
            if (in_array('tw', $article->post_targets, true)) {
                (new TwitterOAuth(
                    config('sqwh.tw.consumer_key'),
                    config('sqwh.tw.consumer_secret'),
                    config('sqwh.tw.access_token'),
                    config('sqwh.tw.access_token_secret')
                ))->post("statuses/update", [
                    "status" => trim(trim($article->content) . "\n" . trim($article->link)),
                ]);
                Log::info('posted to twitter');
            }
            if (in_array('mstdn', $article->post_targets, true)) {
                (new MastodonApiRepository())->post(
                    trim(trim($article->content) . "\n" . trim($article->link)),
                );
                Log::info('posted to mastodon');
            }
        }

        $article->fill(['posted_at' => new DateTime()])->save();
    }
}
