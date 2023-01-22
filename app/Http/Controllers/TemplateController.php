<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\StoreTemplateRequest;
use App\Http\Requests\UpdateTemplateRequest;
use App\Models\Template;
use App\Repositories\CategoryRepository;
use App\Repositories\TemplateRepository;

class TemplateController extends Controller
{
    /**
     * The category repository implementation.
     *
     * @var CategoryRepository
     */
    protected $categories;

    /**
     * The template repository implementation.
     *
     * @var TemplateRepository
     */
    protected $templates;

    /**
     * Create a new controller instance.
     *
     * @param  CategoryRepository  $categories
     * @param  TemplateRepository  $templates
     * @return void
     */
    public function __construct(
        CategoryRepository $categories,
        TemplateRepository $templates
    ) {
        $this->categories = $categories;
        $this->templates = $templates;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('templates.index', [
            'templates' => $this->templates->list(true),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('templates.edit', [
            'template' => new Template([
                'used_at' => (new DateTime('2000/01/01'))->format(DATETIME_LOCAL),
            ]),
            'categories' => $this->categories->list(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTemplateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTemplateRequest $request)
    {
        Template::create($request->validated());

        return Redirect::route('templates.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function edit(Template $template)
    {
        return view('templates.edit', [
            'template' => $template,
            'categories' => $this->categories->list(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTemplateRequest  $request
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTemplateRequest $request, Template $template)
    {
        $template->fill($request->validated());
        $template->save();

        return Redirect::route('templates.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function destroy(Template $template)
    {
        $template->forceDelete();

        return Redirect::route('templates.index');
    }

    /**
     * Enable the specified resource.
     *
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function enable(Template $template)
    {
        $template->restore();

        return Redirect::route('templates.index');
    }

    /**
     * Disable the specified resource.
     *
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function disable(Template $template)
    {
        $template->delete();

        return Redirect::route('templates.index');
    }
}
