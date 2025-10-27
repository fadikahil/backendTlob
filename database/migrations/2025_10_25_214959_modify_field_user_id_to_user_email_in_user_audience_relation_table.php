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
        Schema::table('user_audience_relations', function (Blueprint $table) {
            $table->dropForeign('user_audience_relations_user_id_foreign');
            $table->dropColumn('user_id');
            $table->string('user_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_audience_relations', function (Blueprint $table) {
            $table->dropColumn('user_email');
        });
    }
};
