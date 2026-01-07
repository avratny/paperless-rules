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
        Schema::create('rule_execution_logs', function (Blueprint $table) {
            $table->id();
            $table->string('job_id')->index(); // Unique job ID for grouping
            $table->unsignedBigInteger('document_id');
            $table->foreignId('rule_id')->nullable()->constrained('rules')->onDelete('set null');
            $table->string('rule_name'); // Store rule name in case rule is deleted
            $table->integer('rule_order')->default(0);
            $table->string('event_type'); // 'on_create' or 'on_change'
            $table->enum('status', ['success', 'error', 'skipped'])->default('success');
            $table->text('error_message')->nullable();
            $table->json('trace')->nullable(); // Store execution trace
            $table->boolean('document_modified')->default(false);
            $table->timestamp('executed_at');
            $table->timestamps();

            // Indexes for better query performance
            $table->index('document_id');
            $table->index('rule_id');
            $table->index('event_type');
            $table->index('status');
            $table->index('executed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rule_execution_logs');
    }
};
