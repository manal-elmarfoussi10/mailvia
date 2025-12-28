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
        Schema::table('inbox_tests', function (Blueprint $table) {
            $table->string('from_name')->nullable()->after('name');
            $table->string('from_email')->nullable()->after('from_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inbox_tests', function (Blueprint $table) {
            $table->dropColumn(['from_name', 'from_email']);
        });
    }
};
