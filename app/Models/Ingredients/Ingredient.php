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
            $query->where('name', 'like', '%' . $data->search . '%');
                //->orWhere('description', 'like', '%' . $data->search . '%');
        }

        return $query;
    }
}
