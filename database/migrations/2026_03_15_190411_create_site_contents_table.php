<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_contents', function (Blueprint $table) {
            $table->id();
            $table->string('key');           // e.g. "home.greeting", "about.body"
            $table->string('locale', 10);    // "zh-TW" or "en"
            $table->text('content');
            $table->string('label')->nullable(); // human-readable label for admin, e.g. "Homepage Greeting"
            $table->string('type')->default('text'); // "text" or "textarea" (for admin UI)
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['key', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_contents');
    }
};