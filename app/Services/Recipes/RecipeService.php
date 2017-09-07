<?php

namespace App\Services\Recipes;

use App\Models\Recipes\Recipe;
use App\Models\Recipes\RecipeFile;
use App\Models\Ingredients\Ingredient;
use App\Models\Ingredients\IngredientRecipe;
use App\Serices\Ingredients\IngredientService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;

/**
 * Recipe Service
 * 
 * @category Recipe
 * @package  RecipeService
 * @author   Jose Suarez <chua.jose@gmail.com>
 * @license  MIT http://fsf.org/
 * @link     http://url.com
 */
class RecipeService
{
    private $recipe = false;
    /**
     * Create recipe
     *
     * @return void
     */
    function createRecipe($data)
    {
        $this->recipe = new Recipe;
        $this->recipe->name = $data->name;
        $this->recipe->description = $data->description;
        $this->recipe->save();
        return $this;
    }

    function getRecipe($uid)
    {
        $this->recipe = Recipe::find($uid);
        return $this->recipe;
    }

}