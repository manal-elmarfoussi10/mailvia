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
        Schema::table('domains', function (Blueprint $table) {
            $table->string('provider_type')->default('smtp')->after('domain');
            $table->text('dkim_tokens')->nullable()->after('dmarc_verified')->comment('JSON array of SES DKIM tokens');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn(['provider_type', 'dkim_tokens']);
        });
    }
};
