<?php

namespace App\Http\Controllers;

use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\TodoList;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TodoListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allLists = TodoList::orderBy('created_at', 'desc')->get();
        foreach ($allLists as $list) {
            TodoListController::mergeItems($list);
        }
        return SuccessResponse::getResourceResponse($allLists);
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
            $newList = new TodoList;
            $newList->name = $request->name;
            $newList->save();
            return SuccessResponse::getResourceResponse($newList);   
        } catch(\Exception $e) {     
            return ErrorResponse::getMaliciousResponse();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $existingList = TodoList::find($id);
        if($existingList) {
            TodoListController::mergeItems($existingList);
            return SuccessResponse::getResourceResponse($existingList);
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
        $existingList = TodoList::find($id);
        if($existingList) {
            $existingList->name = $request->name ?? $existingList->name;
            $existingList->updated_at = Carbon::now();
            $existingList->save();
            return SuccessResponse::getResourceResponse($existingList);
        }
        return ErrorResponse::getNotFoundResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $existingList = TodoList::find($id);
        if($existingList) {
           $existingList->delete();
           return SuccessResponse::getMessageResponse('List deleted');
        }
        return ErrorResponse::getNotFoundResponse();
    }

    /**
     * Merge todo items from database into given list.
     */
    public static function mergeItems(TodoList $list) 
    {
        $items = ItemController::whereListId($list->id);
        $list->todos = $items;
    }
}
