<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->boolean('is_ab_test')->default(false)->after('status');
            $table->json('ab_variations')->nullable()->after('is_ab_test');
            $table->string('ab_winner_criteria')->nullable()->after('ab_variations'); // 'open_rate', 'click_rate'
            $table->integer('ab_test_duration')->nullable()->after('ab_winner_criteria'); // hours
            $table->decimal('ab_test_sample_size', 5, 2)->nullable()->after('ab_test_duration'); // percentage
            $table->string('ab_winner_id')->nullable()->after('ab_test_sample_size'); // variation key
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'is_ab_test', 
                'ab_variations', 
                'ab_winner_criteria', 
                'ab_test_duration', 
                'ab_test_sample_size',
                'ab_winner_id'
            ]);
        });
    }
};
