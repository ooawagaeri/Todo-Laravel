<?php

namespace App\Http\Controllers;

use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
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
        return SuccessResponse::getResourceResponse(
            Item::orderBy('created_at', 'desc')->get()
        );
    }

    /**
     * Display a listing of the resource given a list id.
     */
    public static function whereListId(string $id)
    {
        return Item::where('todo_list_id', $id)->get();
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
            $newItem->description = $request->description;
            $newItem->todo_list_id = $request->todo_list_id;
            $newItem->save();
            return SuccessResponse::getResourceResponse($newItem);
        } catch(\Exception $e) {
            return ErrorResponse::getMaliciousResponse();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $existingItem = Item::find($id);
        if($existingItem) {
            return SuccessResponse::getResourceResponse($existingItem);
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
            $existingItem->description = $request->description ?? $existingItem->description;
            $existingItem->is_done = ($request->is_done ?? $existingItem->is_done) ? true : false;
            $existingItem->updated_at = Carbon::now() ;
            $existingItem->save();
            return SuccessResponse::getResourceResponse($existingItem);
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
           return SuccessResponse::getMessageResponse('Item deleted');
        }
        return ErrorResponse::getNotFoundResponse();
    }
}
