<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeOpinion;
use App\Models\RecipeType;
use App\Rules\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipeCardController extends Controller
{

    /**
     * Page d'accueil
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $response = [];
        // Récupération de la recette grâce à son od
        $response['recipe'] = Recipe::select('id', 'user_id', 'name', 'servings', 'cooking_time as cookingTime', 'making_time as makingTime', 'image', 'score', 'recipe_type_id', 'vegan_compatible', 'vegetarian_compatible', 'gluten_free_compatible', 'halal_compatible', 'kosher_compatible')
            ->withCount('steps') // Nombre d'étapes possède la recette
            ->withCount('ingredients') // Nombre d'ingrédients dans la recette 
            ->findOrFail($id);
        $response['ingredients'] =  Recipe::findOrFail($id)->ingredients;
        $response['steps'] =  Recipe::findOrFail($id)->steps;
        // Si l'utilisateur est connecté 
        $user = Auth::user();
        $response['userId'] = $user->id ?? null;
        // Tous les avis de la recette sauf celui de l'utilisateur connecté
        $response['comments'] =  Recipe::findOrFail($id)->comments->where('user_id', '!=', $response['userId']);
        // Si utilisateur connecté, récupération de son avis sur la recette (+ fav + report)
        $response['opinion'] = !empty($user) ? RecipeOpinion::whereBelongsTo($user)->where('recipe_id', $id)->first() : [];

        $response['type'] = RecipeType::where('id', $response['recipe']->recipe_type_id)->first()->name;
        return view('recipeshow', $response);
    }

    /**
     * Mettre en favori / Signaler une recette
     *
     * @param Request $request
     * @return void
     */
    public function status(Request $request)
    {
        // Quelle est l'ID de la recette?
        $recipeId = $request->route('id');
        // Récupération de l'utilisateur
        $user = Auth::user();

        // Si pas d'utilisateur
        if (!$user) {
            // Déconnexion de l'utilisateur
            Auth::logout();
            return redirect("/");
        }
        $test = $request->validate([
            'is_favorite' => ['boolean', 'nullable'],
            'is_reported' => ['boolean', 'nullable'],
        ]);
        // return dd($test);

        RecipeOpinion::updateOrCreate(
            ['user_id' => $user->id, 'recipe_id' => $recipeId],
            ['is_favorite' => (bool)$request->is_favorite, 'is_reported' => (bool)$request->is_reported]
        );

        return redirect("/recipe/show/$recipeId");
    }

    /**
     * Poster / Créer un commentaire sur la recette
     *
     * @param Request $request
     * @return void
     */
    public function comment(Request $request)
    {
        // Quelle est l'ID de la recette?
        $recipeId = $request->route('id');
        // Récupération de l'utilisateur
        $user = Auth::user();

        // Si pas d'utilisateur
        if (!$user) {
            // Déconnexion de l'utilisateur
            Auth::logout();
            return redirect("/");
        }
        $test = $request->validate([
            'score' => [new Score, 'required'], // Le socre doit passer la règle Score de App/Rules/Score
            'comment' => ['string', 'required'],
        ]);
        // return dd($test);

        RecipeOpinion::updateOrCreate(
            ['user_id' => $user->id, 'recipe_id' => $recipeId],
            ['score' => $request->score, 'comment' => $request->comment]
        );

        // Calcul de la nouvelle note moyenne de la recette
        $recipe = Recipe::findOrFail($recipeId);
        $average = RecipeOpinion::whereBelongsTo($recipe)->avg('score');
        $recipe->score = $average;
        $recipe->save();

        return redirect("/recipe/show/$recipeId");
    }

    /**
     * Supprimer l'opinion et la note de l'utilisateur
     *
     * @param Request $request
     * @return void
     */
    public function emptyOpinion(Request $request)
    {
        // Quelle est l'ID de la recette?
        $recipeId = $request->route('id');
        // Récupération de l'utilisateur
        $user = Auth::user();
        // Si pas d'utilisateur
        if (!$user) {
            // Déconnexion de l'utilisateur
            Auth::logout();
            return redirect("/");
        }
        $recipe = Recipe::findOrFail($recipeId);
        // Trouver l'opinion de la recette par l'utilisateur
        $recipeOpinion = RecipeOpinion::whereBelongsTo($recipe)->whereBelongsTo($user)->firstOrFail();
        if ($recipeOpinion) {
            // Réinitialisation du commentaire et de la note de l'avis sur la recette
            $recipeOpinion->score = null;
            $recipeOpinion->comment = null;
            $recipeOpinion->save();
            // Définition de la nouvelle moyenne
            $average = RecipeOpinion::whereBelongsTo($recipe)->avg('score');
            $recipe->score = $average;
            $recipe->save();
        }

        return redirect("/recipe/show/$recipeId");
    }
}
