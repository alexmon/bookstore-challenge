<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    protected $fillable = ['uuid', 'title', 'isbn', 'author_id', 'is_active'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }
}
