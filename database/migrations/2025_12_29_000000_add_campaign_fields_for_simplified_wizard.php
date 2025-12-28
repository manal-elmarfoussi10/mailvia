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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->string('reply_to')->nullable()->after('from_email');
            $table->integer('eps')->default(10)->after('subject');
            $table->boolean('warmup')->default(false)->after('eps');
            $table->integer('batch_size')->default(100)->after('warmup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['description', 'reply_to', 'eps', 'warmup', 'batch_size']);
        });
    }
};
