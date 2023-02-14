<?php

namespace App\Repositories;

use DateTime;
use App\Models\Article;
use App\Models\Template;

class ArticleRepository
{
    /**
     * List articles not posted with or without trashed.
     *
     * @param bool  $withTrashed
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function list(bool $withTrashed = false)
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
     * List articles not queued.
     *
     * @param DateTime  $before
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function listToBeQueued(DateTime $before)
    {
        $articles = Article::whereNull('posted_at')
            ->whereNull('queued_at')
            ->orderBy('priority')
            ->orderBy('reserved_at')
            ->orderBy('id')
            ->get();
        foreach ($articles as $article) {
            if (getMilliDiff($article->reserved_at, $before) <= 0) {
                yield $article;
            }
        }
    }

    /**
     * Generate and save the article.
     *
     * @param Template  $template
     * @param string  $content
     * @param string  $link
     * @param DateTime|null  $reservedAt
     * @return Article
     */
    public function generate(
        Template $template,
        string $content = '',
        string  $link = '',
        DateTime|null  $reservedAt = null,
    ) {
        $article = Article::create([
            'priority' => $template->category->priority,
            'content' =>  trim(
                str_replace(
                    '%%content%%',
                    $content,
                    $template->body,
                )
            ),
            'link' => $link,
            'reserved_at' => $reservedAt ?: new DateTime(),
        ]);

        $template->fill(['used_at' => new DateTime()])->save();

        return $article;
    }

    /**
     * Generate and save the article from form data.
     *
     * @param array  $formData
     * @return Article
     */
    public function generateFromFormData(array $formData)
    {
        return $this->generate(
            Template::find($formData['template_id']),
            isset($formData['content']) ? $formData['content'] : '',
            isset($formData['link']) ? $formData['link'] : '',
            isset($formData['reserved_at'])
                ? new DateTime($formData['reserved_at'])
                : new DateTime(),
        );;
    }
}
