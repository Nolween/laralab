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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('recipe_type_id')->references('id')->on('recipe_types')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('image')->nullable();
            $table->boolean('is_accepted')->nullable();
            $table->boolean('vegan_compatible')->nullable();
            $table->boolean('vegetarian_compatible')->nullable();
            $table->boolean('gluten_free_compatible')->nullable();
            $table->boolean('halal_compatible')->nullable();
            $table->boolean('kosher_compatible')->nullable();
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
        Schema::dropIfExists('recipes');
    }
};
