<?php

namespace App\Models\Recipes;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $appends = ['ingredients', 'files'];

    /**
     * Get Ingredients recipe model
     *
     * @return void
     */
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
    public function filesRecipes() 
    {
        return $this->hasMany(\App\Models\Recipes\RecipeFile::class);
        //return $this->hasManyThrough(\App\Models\Files\File::class, \App\Models\Recipes\RecipeFile::class);
        
    }

     /**
     * Get files from model
     *
     * @return void ingredient object
     */
    public function getFilesAttribute()
    {
        //dd($this->filesRecipes->files());
        return $this->filesRecipes()->get();
    }

    /**
     * Get Query
     *
     * @param object $query object model database
     * @param array  $data  array with data to search
     * @return void
     */
    public function scopeGetByQuery($query, $data)
    {
        
        if (!is_object($data) || empty($data)) {
            return $query;
        }
        if (isset($data->search)) {
            $query->where('name', 'like', '%' . $data->search . '%')
                ->orWhere('description', 'like', '%' . $data->search . '%');
        }

        return $query;
    }
}
