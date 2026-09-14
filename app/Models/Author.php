<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AuthorFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(AuthorFactory::class)]
class Author extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'name'];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
