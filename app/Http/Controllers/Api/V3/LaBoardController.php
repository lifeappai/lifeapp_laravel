<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V3\LaBoardResource;
use App\Models\LaBoard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LaBoardController extends ResponseController
{
    public function index()
    {
        try {
            $laLevels = LaBoard::orderBy('id', 'desc')->where('status', StatusEnum::ACTIVE)->get();
            $response['boards'] =  LaBoardResource::collection($laLevels);
            return $this->sendResponse($response, "Boards");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage() ,
            ], Response ::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
