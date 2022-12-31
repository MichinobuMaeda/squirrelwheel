<?php

use App\Models\Category;
use App\Models\Template;

function listCategories()
{
    return Category::orderBy('priority')->orderBy('name')->get();
}

function listTemplates()
{
    return Template::all()->sortBy(function($template, $key) {
        return $template->category->priority . ' ' .  $template->category->id . ' ' . $template->name;
    });
}
