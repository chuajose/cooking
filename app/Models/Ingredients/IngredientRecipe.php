<?php

namespace App\Models\Ingredients;

use Illuminate\Database\Eloquent\Model;

class IngredientRecipe extends Model
{
    protected $table = 'ingredients_recipe';

    protected $appends = ['ingredient', 'name'];

    protected $hidden = ['created_at', 'updated_at','ingredient', 'recipe_id'];
    
    /**
     * Get ingredient
     *
     * @return void
     */
    public function ingredient() 
    {
            return $this->belongsTo(\App\Models\Ingredients\Ingredient::class);
    }
    
    /**
     * Get ingredient attribute
     *
     * @return void
     */
    function getIngredientAttribute()
    {
        return $this->ingredient()->first();
    }


     /**
     * Get ingredient attribute
     *
     * @return void
     */
    function getNameAttribute()
    {
        if ($this->ingredient) {
            return $this->ingredient->name;
        }
        return null;
    }

    /**
     * Get recipes use ingredient
     *
     * @return void
     */
    public function recipe() 
    {
        return $this->hasMany(\App\Models\Recipes\Recipe::class);
        
    }
}
