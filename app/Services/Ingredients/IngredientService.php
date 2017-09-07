<?php

namespace App\Services\Ingredients;

use App\Models\Ingredients\Recipe;
use App\Models\Ingredients\RecipeFile;
use App\Models\Ingredients\Ingredient;
use App\Models\Ingredients\IngredientRecipe;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;

/**
 * Ingredients Service
 * 
 * @category Ingredients
 * @package  IngredientService
 * @author   Jose Suarez <chua.jose@gmail.com>
 * @license  MIT http://fsf.org/
 * @link     http://url.com
 */
class IngredientService
{
    private $ingredient = false;

    /**
     * Create new ingredient
     *
     * @param string $name name of ingredient
     * 
     * @return void
     */
    function createIngredient($name)
    {
        $this->ingredient = new Ingredient;
        $this->ingredient->name = $name;
        $this->ingredient->save();
        return $this;
    }
}