<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    protected $fillable = ['uuid', 'name'];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
