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
        Schema::table('leader_boards', function (Blueprint $table) {
            $table->integer('max_points')->default(0)->after('points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leader_boards', function (Blueprint $table) {
            $table->dropColumn('max_points');
        });
    }
};
