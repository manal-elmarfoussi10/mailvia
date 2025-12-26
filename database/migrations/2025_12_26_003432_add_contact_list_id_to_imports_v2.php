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
        Schema::table('imports', function (Blueprint $table) {
            if (!Schema::hasColumn('imports', 'contact_list_id')) {
                $table->foreignId('contact_list_id')->nullable()->constrained()->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('imports', function (Blueprint $table) {
             if (Schema::hasColumn('imports', 'contact_list_id')) {
                 $table->dropForeign(['contact_list_id']);
                 $table->dropColumn('contact_list_id');
             }
        });
    }
};
