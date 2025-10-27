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
        Schema::table('items', function (Blueprint $table) {
            $table->string('audience')->nullable();
            $table->date('item_date')->nullable();
            $table->string('item_time')->nullable();
            $table->string('end_timer_option')->nullable();
            $table->integer('slots')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->removeColumn('audience');
            $table->removeColumn('item_date');
            $table->removeColumn('item_time');
            $table->removeColumn('end_timer_option');
            $table->removeColumn('slots');
        });
    }
};
