<?php

namespace App\Http\Controllers;

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
        return TodoList::orderBy('created_at', 'desc')->get();
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
        $newList = new TodoList;
        $newList->name = $request->list['name'];
        $newList->save();
        return $newList;    
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
            $existingList->name = $request->item['name'] ?? $existingList->name;
            $existingList->updated_at = Carbon::now() ;
            $existingList->save();
            return $existingList;
        }
        return "List not found!";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $existingList = TodoList::find($id);
        if($existingList) {
           $existingList->delete();
           return "List deleted";
        }
        return "List not found!";    
    }
}
