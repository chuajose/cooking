<?php

namespace App\Models\Recipes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RecipeTag extends Model
{
    protected $table = 'recipes_tags';
    protected $appends = ['name'];
    protected $hidden = ['created_at', 'updated_at', 'recipe_id'];

    /**
     * Get files from model
     *
     * @return void ingredient object
     */
    public function tag() 
    {
        return $this->belongsTo(\App\Models\Recipes\Tag::class);
        
    }

    /**
     * Get Ingredients from model
     *
     * @return void ingredient object
     */
    public function getNameAttribute()
    {
        if ($tag = $this->tag()->first()) {
            return $tag->name;
        }
       // return Storage::url($this->file()->first()->name);
        return $this->tag()->first();
    }

    /**
     * Get recupe from model
     *
     * @return void ingredient object
     */
    public function recipe() 
    {
        return $this->belongsTo(\App\Models\Recipes\Recipe::class);
        
    }

    /**
     * Get Ingredients from model
     *
     * @return void ingredient object
     */
    public function getRecipeAttribute()
    {
        return $this->recipe()->get();
    }

}
