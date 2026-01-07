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
        Schema::create('locked_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id')->unique();
            $table->timestamp('locked_at');
            $table->timestamps();

            // Index für schnelle Abfragen
            $table->index('locked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locked_documents');
    }
};
