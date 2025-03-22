<?php

use App\Enums\ContentDirection;
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
        Schema::table('materials', function (Blueprint $table) {
            $table->enum('direction', [ContentDirection::RTL->value, ContentDirection::LTR->value])
                ->default(ContentDirection::RTL->value);
        });

        Schema::table('units', function (Blueprint $table) {
            $table->enum('direction', [
                ContentDirection::RTL->value,
                ContentDirection::LTR->value,
                ContentDirection::INHERIT->value
            ])->default(ContentDirection::INHERIT->value);
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->enum('direction', [
                ContentDirection::RTL->value,
                ContentDirection::LTR->value,
                ContentDirection::INHERIT->value
            ])->default(ContentDirection::INHERIT->value);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->enum('direction', [
                ContentDirection::RTL->value,
                ContentDirection::LTR->value,
                ContentDirection::INHERIT->value
            ])->default(ContentDirection::INHERIT->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('direction');
        });

        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('direction');
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn('direction');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('direction');
        });
    }
};
