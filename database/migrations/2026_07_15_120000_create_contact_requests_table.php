<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Contact / Book-a-Demo requests
|--------------------------------------------------------------------------
| Every "Request a Demo" submission is persisted here before the auto-reply
| (to the customer) and the notification (to CONTACT_EMAIL) are queued.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('job_title')->nullable();
            $table->string('company_name')->nullable();
            $table->json('guests')->nullable();
            $table->string('locale', 5)->default('en');
            $table->timestamps();

            $table->index('email');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_requests');
    }
};
