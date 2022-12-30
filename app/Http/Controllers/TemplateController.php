<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\StoreTemplateRequest;
use App\Http\Requests\UpdateTemplateRequest;
use DateTime;
use App\Models\Category;
use App\Models\Template;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = Template::all()->sortBy(function($template, $key) {
            return $template->category->priority . ' ' .  $template->category->id . ' ' . $template->name;
        });

        return view('templates.index', [
            'templates' => $templates,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $template = new Template;
        $template->used_at = (new DateTime('2000/01/01'))->format('Y-m-d H:i:s');
        $categories = Category::orderBy('priority')->orderBy('name')->get();

        return view('templates.edit', [
            'template' => $template,
            'categories' => $categories,
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
        $validated = $request->validated();
        Template::create($validated)->save();

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
        $categories = Category::orderBy('priority')->orderBy('name')->get();

        return view('templates.edit', [
            'template' => $template,
            'categories' => $categories,
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
        $validated = $request->validated();
        $template->fill($validated);
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
        $template->delete();

        return Redirect::route('templates.index');
    }
}
