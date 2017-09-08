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
     * List ingredient
     *
     * @param object $request data of ingredient
     * 
     * @return void
     */
    function listIngredients($request)
    {
        $list = Ingredient::GetByQuery($request)->paginate(20);
        return $list;
    }

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
        if (!$this->ingredient->save()) {
            return false;
        }
        return $this->ingredient;
    }

    /**
     * Get ingredient
     *
     * @param integer $uid unique id ingredient
     * 
     * @return void
     */
    function getIngredient($uid)
    {
        $this->ingredient = Ingredient::find($uid);
        return $this->ingredient;
    }

    /**
     * Check if ingredient exist
     *
     * @param string $ingredient name ingredient
     * 
     * @return void
     */
    function checkIfExistIngredient($ingredient) 
    {
        $this->ingredient = Ingredient::where('name', $ingredient)->first();

        if (!$this->ingredient) {
            return false;
        }
        return $this->ingredient;

    }
}