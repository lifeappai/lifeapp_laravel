<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\StatusEnum;
use App\Http\Resources\API\V3\LaGradeResource;
use App\Models\LaGrade;
use Illuminate\Http\Response;

class LaGradeController extends ResponseController
{
    public function index()
    {
        try {
            $laGrades = LaGrade::orderByRaw('name * 1 asc') ->where('status', StatusEnum::ACTIVE)->get();
            $response['laGrades'] =  LaGradeResource::collection($laGrades);
            return $this->sendResponse($response, "Grades");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
