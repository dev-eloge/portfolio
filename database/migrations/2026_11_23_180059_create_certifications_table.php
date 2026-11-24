<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->json('title'); // {"fr": ..., "en": ...}
            $table->string('issuer');
            $table->date('issued_at');
            $table->date('expires_at')->nullable(); // null = sans expiration
            $table->string('credential_id')->nullable();
            $table->string('credential_url')->nullable();
            $table->json('skills')->nullable(); // compétences validées (tags)
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
