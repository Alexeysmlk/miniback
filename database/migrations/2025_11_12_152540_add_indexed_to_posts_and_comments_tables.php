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
        Schema::table('posts', function (Blueprint $table) {
            $table->index('author_id');
            $table->index('created_at');
            $table->index(['status', 'published_at']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->index('post_id');
            $table->index('author_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['author_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status', 'published_at']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['post_id']);
            $table->dropIndex(['author_id']);
            $table->dropIndex(['created_at']);
        });
    }
};
