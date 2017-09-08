<?php

namespace App\Models\Recipes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RecipeFile extends Model
{
    protected $table = 'recipes_files';
    protected $appends = ['file'];
    protected $hidden = ['created_at', 'updated_at', 'recipe_id', 'file_id'];

    /**
     * Get files from model
     *
     * @return void ingredient object
     */
    public function file() 
    {
        return $this->belongsTo(\App\Models\Files\File::class);
        
    }

    /**
     * Get Ingredients from model
     *
     * @return void ingredient object
     */
    public function getFileAttribute()
    {
       // return Storage::url($this->file()->first()->name);
        return $this->file()->first();
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
