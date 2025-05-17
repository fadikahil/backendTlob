<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('location');
            $table->text('country')->nullable();
            $table->text('city')->nullable();
            $table->text('state')->nullable();
        });

        DB::table('users')->update(['country' => 'Lebanon', 'city' => 'Beirut', 'state' => 'Beirut Governorate']);;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->addColumn('string', 'location');
            $table->dropColumn('country');
            $table->dropColumn('city');
            $table->dropColumn('state');
        });
    }
};
