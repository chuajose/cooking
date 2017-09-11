<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Services\Recipes\RecipeService;
use App\Http\Requests\Recipes\StoreRecipe;
use App\Http\Requests\Recipes\UpdateRecipe;

class RecipeController extends Controller
{
    function __construct()
    {
        $this->middleware('jwt.auth')->only('store');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $recipe = new RecipeService();
        $data = $recipe->listRecipes($request);
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRecipe $request)
    {
        $recipe = new RecipeService();
        $data = $recipe->createRecipe($request);
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $recipe = new RecipeService();
        $data = $recipe->getRecipe($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request request to update
     * @param int                      $uid     unique id to recipe
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRecipe $request, $uid)
    {
        
        $recipe = new RecipeService();
        
        $data = $recipe->updateRecipe($uid, $request->all());

        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
