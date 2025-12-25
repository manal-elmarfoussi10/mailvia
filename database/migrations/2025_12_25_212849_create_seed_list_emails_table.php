<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seed_list_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seed_list_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->string('mailbox_type')->nullable()->comment('gmail, outlook, yahoo, custom');
            
            // For IMAP connection (optional/advanced)
            $table->string('imap_host')->nullable();
            $table->integer('imap_port')->nullable();
            $table->string('imap_user')->nullable();
            $table->string('imap_password')->nullable(); // Encrypted in model
            $table->string('imap_encryption')->nullable(); // ssl, tls
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seed_list_emails');
    }
};
