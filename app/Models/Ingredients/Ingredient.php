<?php

namespace App\Models\Ingredients;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    
    public function recipes() 
    {
        return $this->hasMany(\App\Models\Recipes\Recipe::class);
        
    }
}
