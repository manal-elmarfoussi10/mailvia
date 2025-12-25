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
        Schema::create('inbox_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('seed_emails'); // JSON array of test email addresses
            $table->foreignId('template_id')->nullable()->constrained()->onDelete('set null');
            $table->string('subject');
            $table->string('status')->default('draft'); // draft, sent, collecting, completed
            $table->json('results')->nullable(); // Inbox placement results
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbox_tests');
    }
};
