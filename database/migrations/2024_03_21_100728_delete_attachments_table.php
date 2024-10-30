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
        Schema::dropIfExists('attachments');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('attachmentable_id');
            $table->string('attachmentable_type');
            $table->tinyText('domain');
            $table->tinyText('uri');
            $table->tinyText('content_type');
            $table->tinyText('title')->nullable();
            $table->timestamps();
        });
    }
};
