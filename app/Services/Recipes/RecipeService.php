<?php

namespace App\Services\Recipes;

use App\Models\Recipes\Recipe;
use App\Models\Recipes\RecipeFile;
use App\Models\Ingredients\Ingredient;
use App\Models\Ingredients\IngredientRecipe;
use App\Models\Recipes\Tag;
use App\Models\Recipes\RecipeTag;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Ingredients\IngredientService;
use App\Services\Tags\tagService;

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
        $this->recipe->difficulty = $data->difficulty;
        $this->recipe->course = $data->course;
        $this->recipe->calories = ($data->calories )? $data->calories : null;
        $this->recipe->duration = ($data->duration )? $data->duration : null;
        
        if (!$this->recipe->save()) {
            DB::rollBack();
            return false;
        }
        if ($data->ingredients && is_array($data->ingredients)) {
            $this->addIngredientsToRecipe($data->ingredients);
        }


        if ($data->tags && is_array($data->tags)) {
            $this->addTagsToRecipe($data->tags);
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
     * Update recipe
     *
     * @param integer $uid  unique id to recipe
     * @param object  $data object request to update recipe
     * 
     * @return void
     */
    function updateRecipe($uid, $request)
    {
        if (empty($request)) {
            return false;
        }
        $fieldRecipe = ['name' , 'description', 'duration', 'difficulty', 'calories'];
        $dataRecipe = [];
        foreach ($request as $key => $value) {

            if (in_array($key, $fieldRecipe)) {
                $dataRecipe[$key] = $value;
            }
        }
        
        $recipe = Recipe::find($uid);
        $recipe->fill($dataRecipe);
        $recipe->save();

        return $recipe;
        
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
     * Add tags to recipe
     *
     * @param array $tags data tags
     * 
     * @return void
     */
    function addTagsToRecipe($tags)
    {
        if (is_array($tags) && !empty($tags)) {
            foreach ($tags as $tagRecipe) {
                
                if (!is_numeric($tagRecipe)) {
                    $tag = $this->addTag($tagRecipe);
                } else {
                    $tag = Tag::find($tagRecipe); 
                } 
                $tagRecipe = $tag;
                $this->addTagToRecipe($tagRecipe);
            }
        }
    }

     /**
     * Add tag to recipe
     *
     * @param array $tag include object tag
     * 
     * @return void
     */
    function addTagToRecipe($tag)
    {
       
        $tagRecipe = new RecipeTag;
        $tagRecipe->recipe_id = $this->recipe->id;
        $tagRecipe->tag_id = $tag->id;
        if (!$tagRecipe->save()) {
            DB::rollBack();
            return false;
        }

        return true;
    }

    /**
     * Add tag to database
     *
     * @param string $tag name ingredient
     * 
     * @return void
     */
    function addTag($tag)
    {
        $tagService = new tagService();
        if ($tagExist = $tagService->checkIfExistTag($tag)) {
            
            return $tagExist;
        }
        
        $tag = $tagService->createTag($tag);
        if (!$tag) {
            DB::rollBack();
            return false;
        }
        return $tag;

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