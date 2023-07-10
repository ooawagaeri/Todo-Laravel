<?php

namespace App\Http\Responses;

class SuccessResponse
{
    /**
     * Throws 201 Not Found response.
     */
    public static function getResourceResponse(mixed $data)
    {
        return response()->json([
            'data' => $data,
        ], 201);
    }

    /**
     * Throws 200 Bad Request response.
     */
    public static function getMessageResponse(string $msg)
    {
        return response()->json([
            'msg' => $msg,
        ], 200);
    }
}