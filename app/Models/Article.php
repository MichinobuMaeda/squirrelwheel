<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Interact with the article's fpost_targets.
     */
    protected function postTargets(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => preg_split('/[\s,]+/', $value ?: ''),
            set: fn (array $value) => implode(' ', $value ?: []),
        );
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'priority',
        'content',
        'link',
        'post_targets',
        'reserved_at',
        'queued_at',
        'posted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'priority' => 'integer',
        'reserved_at' => 'datetime',
        'queued_at' => 'datetime',
        'posted_at' => 'datetime',
    ];
}
