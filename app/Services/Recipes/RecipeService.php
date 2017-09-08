<?php

namespace App\Services\Recipes;

use App\Models\Recipes\Recipe;
use App\Models\Recipes\RecipeFile;
use App\Models\Ingredients\Ingredient;
use App\Models\Ingredients\IngredientRecipe;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Ingredients\IngredientService;
use App\Exceptions\NotCreateRecipeException;
/**
 * Recipe Service
 * 
 * @category Recipe
 * @package  RecipeService
 * @author   Jose Suarez <chua.jose@gmail.com>
 * @license  MIT http://fsf.org/
 * @link     http://url.com
 */
class RecipeService extends IngredientService
{
    private $recipe = false;

    /**
     * List reciopes
     *
     * @param object $request object with data request
     * 
     * @return void
     */
    function listRecipes($request)
    {
        $list = Recipe::GetByQuery($request)->paginate(20);
        return $list;
    }
    /**
     * Create recipe
     *
     * @return void
     */
    function createRecipe($data)
    {
        DB::beginTransaction();
        $this->recipe = new Recipe;
        $this->recipe->name = $data->name;
        $this->recipe->description = $data->description;
        if (!$this->recipe->save()) {
            DB::rollBack();
            return false;
        }
        if ($data->ingredients && is_array($data->ingredients)) {
            $this->addIngredientsToRecipe($data->ingredients);
        }

        if ($data->file) {

            $file = new \App\Services\FileService;
            $upload = $file->upload($data->file);

            if ($upload) {
                $recipeFile = new RecipeFile;
                $recipeFile->file_id = $upload->id;
                $recipeFile->recipe_id = $this->recipe->id;
                $recipeFile->save();
            }
        }
        DB::commit();
        return $this;
    }

    /**
     * Add ingredients to recipe
     *
     * @param array $ingredients data ingredients
     * 
     * @return void
     */
    function addIngredientsToRecipe($ingredients)
    {
        if (is_array($ingredients) && !empty($ingredients)) {
            foreach ($ingredients as $ingredientRecipe) {
                if (!is_array($ingredientRecipe) || (!isset($ingredientRecipe['name']) && !isset($ingredientRecipe['amount']))) {
                    DB::rollBack();
                    return false;
                }

                if (!is_numeric($ingredientRecipe['name'])) {
                    $ingredient = $this->addIngredient($ingredientRecipe['name']);
                } else {
                    $ingredient = Ingredient::find($ingredientRecipe['name']); 
                } 
                $ingredientRecipe['ingredient'] = $ingredient;
                $this->addIngredientToRecipe($ingredientRecipe);
            }
        }
    }
    /**
     * Add ingredient to recipe
     *
     * @param array $ingredient include object ingredient and amount
     * 
     * @return void
     */
    function addIngredientToRecipe($ingredient)
    {
        if (!is_array($ingredient) || !isset($ingredient['ingredient'])|| !isset($ingredient['amount'])) {
            DB::rollBack();
            return false;
        }
        $ingredientRecipe = new IngredientRecipe;
        $ingredientRecipe->recipe_id = $this->recipe->id;
        $ingredientRecipe->ingredient_id = $ingredient['ingredient']->id;
        $ingredientRecipe->amount = $ingredient['amount'];
        if (!$ingredientRecipe->save()) {
            DB::rollBack();
            return false;
        }

    }

    /**
     * Add ingredient to database
     *
     * @param string $ingredient name ingredient
     * 
     * @return void
     */
    function addIngredient($ingredient)
    {
        
        if ($ingredientExist = $this->checkIfExistIngredient($ingredient)) {
            
            return $ingredientExist;
        }
        
        $ingredient = $this->createIngredient($ingredient);
        if (!$ingredient) {
            DB::rollBack();
            return false;
        }
        return $ingredient;

    }

     /**
     * Get recipe by id
     * 
     * @param integer $uid unique uid recipe
     *
     * @return void
     */
    function getRecipe($uid)
    {
        $this->recipe = Recipe::find($uid);
        return $this->recipe;
    }

}