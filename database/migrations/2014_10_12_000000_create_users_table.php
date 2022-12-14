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
        Schema::create("users", function (Blueprint $table) {
            $table->id();
            $table->string("first_name");
            $table->string("last_name");
            $table->string("username")->unique();
            $table->string("email")->unique();
            $table->timestamp("email_verified_at")->nullable();
            $table->string("password")->min(8);
            $table->enum("status", ["active", "inactive", "pending"])->default("inactive");
            $table->integer("role_id");
            $table->rememberToken();
            $table->timestamps();

            $table->index(["status", "role_id"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("users");
    }
};
