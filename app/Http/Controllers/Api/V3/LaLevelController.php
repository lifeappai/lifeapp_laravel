<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\StatusEnum;
use App\Http\Resources\API\V3\LaLevelResource;
use App\Models\LaLevel;
use Illuminate\Http\Response;

class LaLevelController extends ResponseController
{
    public function index()
    {
        try {
            $laLevels = LaLevel::where('status', StatusEnum::ACTIVE)->get();
            $response['laLevels'] =  LaLevelResource::collection($laLevels);
            return $this->sendResponse($response, "Levels");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
