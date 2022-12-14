<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("admins", function (Blueprint $table) {
            $table->id();
            $table->string("first_name")->max(255);
            $table->string("last_name")->max(255);
            $table->string("username")->max(255)->unique();
            $table->string("email")->max(255)->unique();
            $table->foreignId("role_id")->constrained()->cascadeOnDelete();
            $table->string("password")->min(8)->max(255);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("admins");
    }
};
