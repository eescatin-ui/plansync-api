<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->string('subtitle')->nullable()->after('title');
            $table->string('type')->default('personal')->after('remind_at');
            $table->string('color')->default('#4361ee')->after('type');
            $table->string('icon')->default('bell')->after('color');
        });
    }

    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropColumn(['subtitle', 'type', 'color', 'icon']);
        });
    }
};