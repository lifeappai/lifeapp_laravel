<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class ResponseController extends Controller
{
    /**
     * return success response method of details.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'status'  => Response::HTTP_OK,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response, Response::HTTP_OK);
    }


    /**
     * return error response method of details
     *
     * @param $errorMessage
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($errorMessage, $code = Response::HTTP_BAD_REQUEST)
    {
        $response = [
            'status' => $code,
            'message' => $errorMessage,
        ];
        return response()->json($response, $code);
    }

    /**
     * return error response method of details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendUnauthorizedError()
    {
        return response()->json([
            'code' => 401,
            'error' => 'Unauthorized!!'
        ], 401);
    }
}
