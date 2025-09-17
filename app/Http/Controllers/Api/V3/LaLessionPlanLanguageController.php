<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\StatusEnum;
use App\Http\Resources\API\V3\LaLessionPlanLanguageResource;
use App\Models\LaLessionPlanLanguage;
use Illuminate\Http\Response;

class LaLessionPlanLanguageController extends ResponseController
{
    public function index()
    {
        try {
            $laLessionPlanLanguages = LaLessionPlanLanguage::orderBy('id', 'desc')->where('status', StatusEnum::ACTIVE)->get();
            $response['laLessionPlanLanguages'] =  LaLessionPlanLanguageResource::collection($laLessionPlanLanguages);
            return $this->sendResponse($response, "Lession Plan Languages");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
