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
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('domain')->unique();
            $table->string('verification_token')->unique();
            $table->string('status')->default('unverified'); // unverified, pending, verified, failed
            $table->boolean('spf_verified')->default(false);
            $table->boolean('dkim_verified')->default(false);
            $table->boolean('dmarc_verified')->default(false);
            $table->timestamps();

            $table->index(['company_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
