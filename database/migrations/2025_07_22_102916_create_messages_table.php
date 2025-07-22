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
        Schema::create('messages', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for the message
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade'); // Link to the vendor
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade'); // Link to the user (driver/staff) who sent the message
            $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade'); // Link to the user (driver/staff) who received the message

            $table->text('message_content');
            $table->timestamp('read_at')->nullable(); // Timestamp when the message was read
            
            // Optional: If you want to group messages into explicit conversations
            // $table->foreignId('conversation_id')->nullable()->constrained('conversations')->onDelete('cascade');

            $table->timestamps(); // created_at and updated_at (which will be sent_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema->dropIfExists('messages');
    }
};