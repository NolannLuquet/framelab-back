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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('lastname');
            $table->string('firstname');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('verified')->default(false);
            $table->string('password');
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('theme');
            $table->text('description');
            $table->string('category');
            $table->string('base_photo');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('participations', function (Blueprint $table) {
            $table->id();
            $table->string('photo');
            $table->text('description')->nullable();
            $table->dateTime('submission_date');
            $table->boolean('hidden')->default(false);
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('challenge_id')->constrained('challenges');
            $table->unique(['user_id', 'challenge_id']);
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('label')->unique();
            $table->timestamps();
        });

        Schema::create('associate', function (Blueprint $table) {
            $table->foreignId('participation_id')->constrained('participations');
            $table->foreignId('tag_id')->constrained('tags');
            $table->primary(['participation_id', 'tag_id']);
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->dateTime('comment_date');
            $table->boolean('hidden')->default(false);
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('participation_id')->constrained('participations');
            $table->timestamps();
        });

        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->integer('creativity');
            $table->integer('technique');
            $table->integer('theme_respect');
            $table->dateTime('vote_date');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('participation_id')->constrained('participations');
            $table->unique(['user_id', 'participation_id']);
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('reason');
            $table->dateTime('report_date');
            $table->string('status')->default('pending');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('participation_id')->nullable()->constrained('participations');
            $table->foreignId('comment_id')->nullable()->constrained('comments');
            $table->unique(['user_id', 'participation_id']);
            $table->unique(['user_id', 'comment_id']);
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('votes');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('associate');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('participations');
        Schema::dropIfExists('challenges');
        Schema::dropIfExists('users');
    }
};
