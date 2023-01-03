<?php

use Illuminate\Support\Facades\Log;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\Article;
use App\Models\Category;
use App\Models\Template;

/**
 * List all categories with or without trashed.
 *
 * @param boolean  $withTrashed
 * @return Illuminate\Database\Eloquent\Collection
 */
function listCategories($withTrashed = false)
{
    return ($withTrashed
        ? Category::withTrashed()->orderBy('priority')
        : Category::orderBy('priority'))
            ->orderBy('name')
            ->get();
}

/**
 * List all templates with or without trashed.
 *
 * @param boolean  $withTrashed
 * @return Illuminate\Database\Eloquent\Collection
 */
function listTemplates($withTrashed = false)
{
    return ($withTrashed
        ? Template::withTrashed()->get()
        : Template::all())
            ->sortBy(function($template, $key) {
                return $template->category->priority . ' ' .
                    $template->category->id . ' ' .
                    $template->name;
            });
}

/**
 * List articles not posted with or without trashed.
 *
 * @param boolean  $withTrashed
 * @return Illuminate\Database\Eloquent\Collection
 */
function listArticles($withTrashed = false)
{
    return ($withTrashed
        ? Article::withTrashed()->whereNull('posted_at')
        : Article::whereNull('posted_at'))
            ->orderBy('priority')
            ->orderBy('reserved_at')
            ->orderBy('id')
            ->get();
}

/**
 * Generate and save the article.
 *
 * @param App\Models\Template  $template
 * @param string  $content
 * @param string  $link
 * @param \DateTime  $reservedAt
 * @return App\Models\Article
 */
function generateArticle($template, $content = '', $link = '', $reservedAt = null)
{
    $article = Article::create([
        'priority' => $template->category->priority,
        'content' =>  trim(
            str_replace(
                '%%link%%',
                $link,
                str_replace(
                    '%%content%%',
                    $content,
                    $template->body,
                )
            )
        ),
        'reserved_at' => $reservedAt ?: new DateTime(),
    ]);

    $template->used_at = new DateTime();
    $template->save();

    return $article;
}

/**
 * Post the article.
 *
 * @param App\Models\Article  $article
 * @return void
 */
function postArticle($article)
{
    Log::info('article ID: ' . strval($article->id) . ' priority: ' . strval($article->priority));

    if (config('app.env') === 'production') {
        postToTwitter($article);
    }

    $article->posted_at = new DateTime();
    $article->save();
}

/**
 * Post the article to twitter.
 *
 * @param App\Models\Article  $article
 * @return void
 */
function postToTwitter($article)
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
