<?php

namespace App\Http\Controllers;

use App\Http\Responses\ErrorResponse;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Item::orderBy('created_at', 'desc')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $newItem = new Item;
            $newItem->description = $request->item['description'];
            $newItem->todo_list_id = $request->item['todo_list_id'];
            $newItem->save();
            return $newItem;
        } catch(\Exception $e) {
            return ErrorResponse::getNotFoundResponse();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $existingItem = Item::find($id);
        if($existingItem) {
            return $existingItem;
        }
        return ErrorResponse::getNotFoundResponse();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $existingItem = Item::find($id);
        if($existingItem) {
            $existingItem->description = $request->item['description'] ?? $existingItem->description;
            $existingItem->is_done = ($request->item['is_done'] ?? $existingItem->is_done) ? true : false;
            $existingItem->updated_at = Carbon::now() ;
            $existingItem->save();
            return $existingItem;
        }
        return ErrorResponse::getNotFoundResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $existingItem = Item::find($id);
        if($existingItem) {
           $existingItem->delete();
           return "Item deleted";
        }
        return ErrorResponse::getNotFoundResponse();
    }
}
