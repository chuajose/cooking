<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Ingredients\IngredientService;
use App\Http\Requests\Ingredients\StoreIngredient;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param \Illuminate\Http\Request $request object request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ingredient = new IngredientService;
        return response()->json($ingredient->listIngredients($request));
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
     * @param \Illuminate\Http\Request $request object request
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(StoreIngredient $request)
    {
        $ingredient = new IngredientService;
        $data = $ingredient->createIngredient($request->input('name'));
        return response()->json(['create' => $data]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $uid unique id resource
     * 
     * @return \Illuminate\Http\Response
     */
    public function show($uid)
    {
        $ingredient = new IngredientService;
        return response()->json($ingredient->getIngredient($uid));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id unique id object
     * 
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
     * @param int                      $id      unique id for ingrediente
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id unique id object
     * 
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
