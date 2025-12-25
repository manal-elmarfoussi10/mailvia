<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            
            $table->enum('status', ['pending', 'sent', 'delivered', 'bounced', 'failed', 'complained'])->default('pending');
            
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            
            $table->text('error_message')->nullable();
            $table->integer('attempts')->default(0);
            
            $table->timestamps();
            
            $table->index(['campaign_id', 'status']);
            $table->index(['contact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_sends');
    }
};
