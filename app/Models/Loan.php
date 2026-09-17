<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Loan extends Pivot
{
    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = ['book_id', 'borrower_id', 'action', 'date_captured'];

    protected $table = 'loans';
}
