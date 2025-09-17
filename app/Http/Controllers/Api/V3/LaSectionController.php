<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\StatusEnum;
use App\Http\Resources\API\V3\LaSectionResource;
use App\Models\LaSection;
use Illuminate\Http\Response;

class LaSectionController extends ResponseController
{
    public function index()
    {
        try {
            $laSections = LaSection::orderBy('name')->where('status', StatusEnum::ACTIVE)->get();
            $response['laSections'] =  LaSectionResource::collection($laSections);
            return $this->sendResponse($response, "Levels");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
