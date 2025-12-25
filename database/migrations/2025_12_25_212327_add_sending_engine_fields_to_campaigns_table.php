<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // Modify existing string status to enum if possible, or just add new columns
            if (Schema::hasColumn('campaigns', 'status')) {
                // We can't easily change to enum in a portable way without doctrine/dbal or similar
                // For now, let's just make sure we have the other columns
            } else {
                $table->enum('status', ['draft', 'scheduled', 'sending', 'paused', 'completed', 'stopped'])
                      ->default('draft')
                      ->after('template_id');
            }
            
            if (!Schema::hasColumn('campaigns', 'paused_at')) {
                $table->timestamp('paused_at')->nullable()->after('completed_at');
            }
            
            if (!Schema::hasColumn('campaigns', 'throttle_rate')) {
                $table->integer('throttle_rate')->default(10)->after('paused_at')->comment('Emails per second');
            }
            
            if (!Schema::hasColumn('campaigns', 'throttle_concurrency')) {
                $table->integer('throttle_concurrency')->default(3)->after('throttle_rate')->comment('Concurrent jobs');
            }
            
            if (!Schema::hasColumn('campaigns', 'delivered_count')) {
                $table->integer('delivered_count')->default(0)->after('sent_count');
            }
            
            if (!Schema::hasColumn('campaigns', 'failed_count')) {
                $table->integer('failed_count')->default(0)->after('delivered_count');
            }
            
            if (!Schema::hasColumn('campaigns', 'bounced_count')) {
                $table->integer('bounced_count')->default(0)->after('failed_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'paused_at',
                'throttle_rate',
                'throttle_concurrency',
                'delivered_count',
                'failed_count',
                'bounced_count',
            ]);
        });
    }
};
