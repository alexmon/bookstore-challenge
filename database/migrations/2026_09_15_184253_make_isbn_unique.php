<?php

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
        // make isbn column unique
        Schema::table('books', function (Blueprint $table) {
            $table->string('isbn')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // remove unique constraint from isbn column
        Schema::table('books', function (Blueprint $table) {
            $table->string('isbn')->unique(false)->change();
        });
    }
};
