<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipe_type_id',
        'name',
        'image',
        'is_accepted',
        'vegan_compatible',
        'vegetarian_compatible',
        'gluten_free_compatible',
        'halal_compatible',
        'kosher_compatible',
    ];

    /**
     * Indique à quel type de recette elle appartient
     *
     * @return void
     */
    public function recipeType()
    {
        return $this->belongsTo(RecipeType::class);
    }

    /**
     * Indique à quel utilisateur elle appartient
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Indique les étapes de la recette
     *
     * @return void
     */
    public function steps()
    {
        return $this->hasMany(RecipeStep::class);
    }

    /**
     * Indique les ingrédients de la recette
     *
     * @return void
     */
    public function ingredients()
    {
        return $this->hasMany(RecipeIngredients::class);
    }

    /**
     * Indique les opinions de la recette
     *
     * @return void
     */
    public function opinions()
    {
        return $this->hasMany(RecipeOpinion::class);
    }
}
