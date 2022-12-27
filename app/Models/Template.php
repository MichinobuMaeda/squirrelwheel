<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Category;

class Template extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Get the category that owns the template.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
