<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('campaigns', 'track_opens')) {
                $table->boolean('track_opens')->default(true)->after('status');
            }
            if (!Schema::hasColumn('campaigns', 'track_clicks')) {
                $table->boolean('track_clicks')->default(true)->after('track_opens');
            }
            
            // Stats counters for engagement
            if (!Schema::hasColumn('campaigns', 'open_count')) {
                $table->integer('open_count')->default(0)->after('failed_count');
            }
            if (!Schema::hasColumn('campaigns', 'click_count')) {
                $table->integer('click_count')->default(0)->after('open_count');
            }
            if (!Schema::hasColumn('campaigns', 'complaint_count')) {
                $table->integer('complaint_count')->default(0)->after('bounced_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['track_opens', 'track_clicks', 'open_count', 'click_count', 'complaint_count']);
        });
    }
};
