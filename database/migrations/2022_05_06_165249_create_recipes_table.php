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
            $table->string('image');
            $table->boolean('is_accepted');
            $table->boolean('vegan_compatible');
            $table->boolean('vegetarian_compatible');
            $table->boolean('gluten_free_compatible');
            $table->boolean('halal_compatible');
            $table->boolean('kosher_compatible');
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
