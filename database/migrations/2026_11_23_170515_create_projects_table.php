<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category');
            $table->string('category_slug')->index();
            $table->text('description');
            // Étude de cas : contexte → problème → solution → résultat
            $table->text('context')->nullable();
            $table->text('problem')->nullable();
            $table->text('solution')->nullable();
            $table->text('result')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->json('technologies')->nullable();
            $table->string('url_live')->nullable();
            $table->string('url_code')->nullable();
            $table->boolean('featured')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
