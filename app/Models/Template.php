<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Category;

class Template extends Model
{
    use SoftDeletes;

    /**
     * Get the category that owns the template.
     */
    public function categories()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'body',
    ];
}
