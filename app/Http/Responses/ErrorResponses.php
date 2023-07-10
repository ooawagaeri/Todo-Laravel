<?php

namespace App\Http\Responses;

class ErrorResponse
{
    /**
     * Throws 404 Not Found response.
     */
    public static function getNotFoundResponse()
    {
        return response()->json([
            'errors' => 'Entity not found!',
        ], 404);
    }

    /**
     * Throws 400 Bad Request response.
     */
    public static function getMaliciousResponse()
    {
        return response()->json([
            'errors' => 'Incorrect data provided!',
        ], 400);
    }
}