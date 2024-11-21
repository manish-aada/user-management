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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->text('description')->nullable()->after('phone');
            $table->unsignedBigInteger('role_id')->after('description');
            $table->string('profile_image')->nullable()->after('role_id');

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('description');
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->dropColumn('profile_image');
        });
    }
};
