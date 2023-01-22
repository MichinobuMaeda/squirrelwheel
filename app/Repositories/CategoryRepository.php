<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    /**
     * List all categories with or without trashed.
     *
     * @param bool  $withTrashed
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function list(bool $withTrashed = false)
    {
        return ($withTrashed
            ? Category::withTrashed()->orderBy('priority')
            : Category::orderBy('priority'))
            ->orderBy('name')
            ->get();
    }

    /**
     * List categories for feed.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function listForFeed()
    {
        return Category::whereNotNull('feed')
            ->orderBy('priority')->orderBy('feed')->get();
    }
}
