<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->foreignId('group_id')->nullable()->after('phone')->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('group_id');
            $table->boolean('is_admin')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn(['phone', 'group_id', 'is_active', 'is_admin']);
        });
    }
};
