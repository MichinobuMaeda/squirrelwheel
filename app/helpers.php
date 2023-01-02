<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Template;

function listCategories($withTrashed = false)
{
    return ($withTrashed
        ? Category::withTrashed()->orderBy('priority')
        : Category::orderBy('priority'))
            ->orderBy('name')
            ->get();
}

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

function getFeedCategories() {
    return Category::whereNotNull('feed')
        ->orderBy('priority')
        ->orderBy('feed')
        ->get();
}

function selectTemplateOfCategory($category)
{
    return Template::where('category_id', $category->id)
        ->orderBy('used_at')
        ->first();
}

function getArticlesNotDispatched() {
    return Article::whereNull('posted_at')
        ->whereNull('queued_at')
        ->orderBy('priority')
        ->orderBy('reserved_at')
        ->orderBy('id')
        ->get();
}

/**
 * Save the article.
 *
 * @param App\Models\Template  $template
 * @param string  $content
 * @param string  $link
 * @param \DateTime  $reservedAt
 * @return void
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
}
