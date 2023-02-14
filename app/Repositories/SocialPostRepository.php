<?php

namespace App\Repositories;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Http;
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
            if (in_array('tw', $targets, true)) {
                $this->postToTwitter($article);
                Log::info('posted to twitter');
            }
            if (in_array('mstdn', $targets, true)) {
                $this->postToMastodon($article);
                Log::info('posted to mastodon');
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
        $connection = new TwitterOAuth(
            config('sqwh.tw.consumer_key'),
            config('sqwh.tw.consumer_secret'),
            config('sqwh.tw.access_token'),
            config('sqwh.tw.access_token_secret')
        );
        $status = trim(<<<'EOT'
{trim($article->content)}
{trim($article->link)}
EOT);
        $connection->post("statuses/update", ["status" => $status]);
    }

    /**
     * Post the article to mastodon.
     *
     * @param Article  $article
     * @return void
     */
    protected function postToMastodon(Article $article)
    {
        $status = trim(<<<'EOT'
{trim($article->content)}
{trim($article->link)}
EOT);
        Http::withHeaders([
            'Authorization' => 'Bearer ' . config('sqwh.mstdn.access_token'),
            'Idempotency-Key' => hash('sha256', $article->content),
        ])->asForm()->post(config('sqwh.mstdn.server') . '/api/v1/statuses', [
            'status' => $status,
            'sensitive' => 'false',
            'visibility' => 'public',
            'language' => config('app.locale'),
        ]);
    }
}
