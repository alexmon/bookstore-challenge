<?php

use BookStoreAPI\BookStore\Domain\Models\LoanAction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('loans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('book_id')
                ->constrained('books')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('borrower_id')
                ->constrained('borrowers')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('action', LoanAction::mapCasesToValues())
                ->default(null);

            $table->timestamp('date_captured')->nullable(false)->useCurrent();

            // NOTE: do not use composite - might need to search only by borrower or by book
            $table->index(['book_id']);
            $table->index(['borrower_id']);
            $table->index('date_captured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
