<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\SocialPostRepository;

class ArticleController extends Controller
{
    /**
     * The article repository implementation.
     *
     * @var ArticleRepository
     */
    protected $articles;

    /**
     * The template repository implementation.
     *
     * @var TemplateRepository
     */
    protected $templates;

    /**
     * The template repository implementation.
     *
     * @var SocialPostRepository
     */
    protected $social;

    /**
     * Create a new controller instance.
     *
     * @param  ArticleRepository  $articles
     * @param  TemplateRepository  $templates
     * @param  SocialPostRepository  $social
     * @return void
     */
    public function __construct(
        ArticleRepository $articles,
        TemplateRepository $templates,
        SocialPostRepository $social,
    ) {
        $this->articles = $articles;
        $this->templates = $templates;
        $this->social = $social;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('articles.index', [
            'articles' => $this->articles->list(true),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('articles.edit', [
            'templates' => $this->templates->list(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreArticleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreArticleRequest $request)
    {
        $article = $this->articles->generateFromFormData($request->validated());

        if ($article->priority === 0) {
            $this->social->post($article);
            $article->fill(['posted_at' => new DateTime()])->save();
        }

        return Redirect::route('articles.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        return view('articles.edit', [
            'article' => $article,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateArticleRequest  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateArticleRequest $request, Article $article)
    {
        $article->fill($request->validated())->save();

        return Redirect::route('articles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        $article->forceDelete();

        return Redirect::route('articles.index');
    }

    /**
     * Enable the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function enable(Article $article)
    {
        $article->restore();

        return Redirect::route('articles.index');
    }

    /**
     * Disable the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function disable(Article $article)
    {
        $article->delete();

        return Redirect::route('articles.index');
    }
}
