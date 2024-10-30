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
        Schema::table('files', function(Blueprint $table) {
            $table->renameColumn('attachmentable_id', 'fileable_id');
        });
        Schema::table('files', function(Blueprint $table) {
            $table->renameColumn('attachmentable_type', 'fileable_type');
        });

        Schema::table('images', function(Blueprint $table) {
            $table->renameColumn('attachmentable_id', 'imageable_id');
        });
        Schema::table('images', function(Blueprint $table) {
            $table->renameColumn('attachmentable_type', 'imageable_type');
        });

        Schema::table('links', function(Blueprint $table) {
            $table->renameColumn('attachmentable_id', 'linkable_id');
        });
        Schema::table('links', function(Blueprint $table) {
            $table->renameColumn('attachmentable_type', 'linkable_type');
        });

        Schema::table('videos', function(Blueprint $table) {
            $table->renameColumn('attachmentable_id', 'videoable_id');
        });
        Schema::table('videos', function(Blueprint $table) {
            $table->renameColumn('attachmentable_type', 'videoable_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function(Blueprint $table) {
            $table->renameColumn('fileable_id', 'attachmentable_id');
        });
        Schema::table('files', function(Blueprint $table) {
            $table->renameColumn('fileable_type', 'attachmentable_type');
        });

        Schema::table('images', function(Blueprint $table) {
            $table->renameColumn('imageable_id', 'attachmentable_id');
        });
        Schema::table('images', function(Blueprint $table) {
            $table->renameColumn('imageable_type', 'attachmentable_type');
        });

        Schema::table('links', function(Blueprint $table) {
            $table->renameColumn('linkable_id', 'attachmentable_id');
        });
        Schema::table('links', function(Blueprint $table) {
            $table->renameColumn('linkable_type', 'attachmentable_type');
        });

        Schema::table('videos', function(Blueprint $table) {
            $table->renameColumn('videoable_id', 'attachmentable_id');
        });
        Schema::table('videos', function(Blueprint $table) {
            $table->renameColumn('videoable_type', 'attachmentable_type');
        });
    }
};
