<?php

namespace App\Http\Controllers;

use App\Http\Responses\SuccessResponse;
use App\Models\Item;
use App\Models\TodoList;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    /**
     * Finds intersection between two object lists with Id.
     */
    function findIntersect($list1, $list2) {
        return array_uintersect($list1, $list2, function ($obj1, $obj2) {
            if ($obj1['id'] === $obj2['id']) {
                return 0;
            } elseif ($obj1['id'] < $obj2['id']) {
                return -1;
            } else {
                return 1;
            }});
    }

    /**
     * Finds differences between 1st list difference against 2nd list with Id.
     * Order of parameters matters.
     */
    function findDifference($list1, $list2) {
        return array_udiff($list1, $list2, function ($obj1, $obj2) {
            if ($obj1['id'] === $obj2['id']) {
                return 0;
            } elseif ($obj1['id'] < $obj2['id']) {
                return -1;
            } else {
                return 1;
            }});
    }

    /**
     * Handles deletion of old lists
     */
    function handleDeleteLists($deletingLists) {
        foreach ($deletingLists as $list) {
            $existingList = TodoList::find($list['id']);
            $existingList->delete();
        }
    }

    /**
     * Handles addition of new lists
     */
    function handleAddingLists($addingLists) {
        foreach ($addingLists as $list) {
            $newList = new TodoList;
            $newList->name = $list['name'];
            $newList->save();

            // Since new list, all todos are also new
            foreach ($list['todos'] as $todo) {
                $newItem = new Item;
                $newItem->description = $todo['description'];
                $newItem->todo_list_id = $newList->id;
                $newItem->is_done = $todo['is_done'];
                $newItem->save();
            }
        }
    }


    /**
     * Handles editing of existing lists
     */
    function handleExistingLists($existingLists) {
        foreach ($existingLists as $list) {
            $existingList = TodoList::find($list['id']);
            $existingList->name = $list['name'] ?? $existingList->name;
            $existingList->updated_at = Carbon::now();
            $existingList->save();

            $allItems = Item::where('todo_list_id', $list['id'])->get()->toArray();
            $offlineItems = $list['todos'];

            $deletingItems = $this->findDifference($allItems, $offlineItems);
            $existingItems = $this->findIntersect($offlineItems, $allItems);
            $addingItems = $this->findDifference($offlineItems, $allItems);

            // Deletes old items
            foreach ($deletingItems as $item) {
                $existingItem = Item::find($item['id']);
                $existingItem->delete();
            }
            // Edits/Overrides existing items
            foreach ($existingItems as $item) {
                $existingItem = Item::find($item['id']);
                $existingItem->description = $item['description'] ?? $existingItem->description;
                $existingItem->is_done = ($item['is_done'] ?? $existingItem->is_done) ? true : false;
                $existingItem->updated_at = Carbon::now() ;
                $existingItem->save();
            }
            // Adds new items
            foreach ($addingItems as $item) {
                $newItem = new Item;
                $newItem->description = $item['description'];
                $newItem->todo_list_id = $list['id'];
                $newItem->is_done = $item['is_done'];
                $newItem->save();
            }
    
        }
    }

    /**
     * Updates all resources in storage.
     */
    public function update(Request $request)
    {

        $offlineLists = $request->data;
        $allLists = TodoList::orderBy('created_at', 'desc')->get()->toArray();

        $deletingLists = $this->findDifference($allLists, $offlineLists);
        $existingLists = $this->findIntersect($offlineLists, $allLists);
        $addingLists = $this->findDifference($offlineLists, $allLists);

        $this->handleDeleteLists($deletingLists);
        $this->handleExistingLists($existingLists);
        $this->handleAddingLists($addingLists);

        // Return new lists
        $newLists = TodoList::orderBy('created_at', 'desc')->get();
        foreach ($newLists as $list) {
            TodoListController::mergeItems($list);
        }
        return SuccessResponse::getResourceResponse($newLists);
    }
}
