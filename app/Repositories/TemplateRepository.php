<?php

namespace App\Repositories;

use App\Models\Template;

class TemplateRepository
{
    /**
     * List all templates with or without trashed.
     *
     * @param bool  $withTrashed
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function list(bool $withTrashed = false)
    {
        return ($withTrashed
            ? Template::withTrashed()->get()
            : Template::all())->sortBy(
            function ($template) {
                return $template->category->priority . ' ' .
                    $template->category->id . ' ' .
                    $template->name;
            }
        );
    }
}
