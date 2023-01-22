<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Article;
use App\Repositories\SocialPostRepository;

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
    public function __construct(Article $article) {
        $this->article = $article;
    }

    /**
     * Execute the job.
     *
     * @param App\Models\SocialPostRepository  $social
     * @return void
     */
    public function handle(SocialPostRepository $social)
    {
        config(['logging.default' => 'job']);

        // the article to be refreshed
        $article = Article::find($this->article->id);
        $social->post($article);
    }
}
