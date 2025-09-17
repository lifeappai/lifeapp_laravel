<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\StatusEnum;
use App\Http\Resources\API\V3\LaConceptCartoonHeaderResource;
use App\Http\Resources\API\V3\LaConceptCartoonResource;
use App\Models\LaConceptCartoon;
use App\Models\LaConceptCartoonHeader;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class LaConceptCartoonController extends ResponseController
{
    public function index(Request $request)
    {
        try {
            $validate = array(
                'la_subject_id' => ['required', 'exists:la_subjects,id'],
                'la_level_id' => ['required', 'exists:la_levels,id'],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }
            $laConceptCartoons = LaConceptCartoon::where('status', StatusEnum::ACTIVE)
                ->where('la_level_id', $request->la_level_id)
                ->where('la_subject_id', $request->la_subject_id)
                ->paginate(15);
            $response['laConceptCartoons'] =  LaConceptCartoonResource::collection($laConceptCartoons)->response()->getData(true);
            return $this->sendResponse($response, "Concept Cartoons");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function headers()
    {
        try {
            $laConceptCartoonHeader = LaConceptCartoonHeader::first();
            $data = $laConceptCartoonHeader ? new LaConceptCartoonHeaderResource($laConceptCartoonHeader) : [];
            return $this->sendResponse($data, "Concept Cartoon Header");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
