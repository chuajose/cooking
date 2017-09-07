<?php

namespace App\Models\Recipes;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $appends = ['ingredients'];

    public function ingredientsRecipe() 
    {
        return $this->hasMany('App\Models\Ingredients\IngredientRecipe');
        
    }

    /**
     * Get Ingredients from model
     *
     * @return void ingredient object
     */
    public function getIngredientsAttribute()
    {
        return $this->ingredientsRecipe()->get();
    }

    /**
     * Get files from model
     *
     * @return void ingredient object
     */
    public function files() 
    {
        return $this->hasMany(\App\Models\Files\File::class);
        
    }
}
