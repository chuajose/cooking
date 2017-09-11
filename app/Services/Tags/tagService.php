<?php

namespace App\Services\Tags;

use App\Models\Recipes\Recipe;
use App\Models\Recipes\RecipeFile;
use App\Models\Recipes\Tag;
use App\Models\Recipes\RecipeTag;
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
class TagService
{
    private $tag = false;



    /**
     * Create new tag
     *
     * @param string $name name of tag
     * 
     * @return void
     */
    function createTag($name)
    {
        $this->tag = new Tag;
        $this->tag->name = $name;
        if (!$this->tag->save()) {
            return false;
        }
        return $this->tag;
    }

    /**
     * Get tag
     *
     * @param integer $uid unique id tag
     * 
     * @return void
     */
    function getTag($uid)
    {
        $this->tag = Tag::find($uid);
        return $this->tag;
    }

    /**
     * Check if ingredient exist
     *
     * @param string $ingredient name ingredient
     * 
     * @return void
     */
    function checkIfExistTag($tag) 
    {
        $this->tag = Tag::where('name', $tag)->first();

        if (!$this->tag) {
            return false;
        }
        return $this->tag;

    }
}