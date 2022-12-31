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

function generateArticle($formData)
{
    $template = Template::find($formData['template_id']);

    return [
        'priority' => $template->category->priority,
        'content' => str_replace(
            '%%content%%',
            isset($formData['content']) ? $formData['content'] : '',
            str_replace(
                '%%link%%',
                isset($formData['link']) ? $formData['link'] : '',
                $template->body
            )
        ),
        'reserved_at' => isset($formData['reserved_at'])
            ? new DateTime($formData['reserved_at'])
            : new DateTime(),
    ];
}
