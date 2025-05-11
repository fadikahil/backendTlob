<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_fcm_tokens', function (Blueprint $table) {
            // If the unique index has a specific name, drop it by its name
            $table->dropUnique(['fcm_token']);
        });
    }

    public function down()
    {
        Schema::table('user_fcm_tokens', function (Blueprint $table) {
            // Add back the unique constraint if rolled back
            $table->unique('fcm_token');
        });
    }
};
