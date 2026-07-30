<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Two-way messaging (Figma frames 841-42746 / 841-43294).
 *
 * A conversation is a thread between polymorphic participants (User /
 * Instructor / Admin — the same cross-identity convention the notification
 * inbox uses). Messages belong to a conversation and record their polymorphic
 * sender. Per-participant `last_read_at` drives unread counts. Deliberately
 * separate from the admin broadcast `admin_messages` table, which is one-way.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('subject')->nullable();
            // Optional course context ("Re: Customer Experience Essentials").
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->string('participant_type');   // App\Models\User|Instructor|Admin
            $table->unsignedBigInteger('participant_id');
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            $table->unique(['conversation_id', 'participant_type', 'participant_id'], 'conv_participant_unique');
            $table->index(['participant_type', 'participant_id']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->string('sender_type');        // App\Models\User|Instructor|Admin
            $table->unsignedBigInteger('sender_id');
            $table->text('body');
            $table->timestamps();

            $table->index(['conversation_id', 'id']);
            $table->index(['sender_type', 'sender_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversation_participants');
        Schema::dropIfExists('conversations');
    }
};
