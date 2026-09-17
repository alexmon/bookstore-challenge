<?php

declare(strict_types=1);

namespace App\Models;

use BookStoreAPI\BookStore\Domain\Models\Loan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = ['uuid', 'title', 'isbn', 'author_id', 'is_active', 'borrower_id'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
