<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V3\LaCompetencyResource;
use App\Models\LaCompetency;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class LaCompetencyController extends ResponseController
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
            $laCompetenies = LaCompetency::where('status', StatusEnum::ACTIVE)
                ->where('la_level_id', $request->la_level_id)
                ->where('la_subject_id', $request->la_subject_id)
                ->paginate(15);
            $response['laCompetenies'] =  LaCompetencyResource::collection($laCompetenies)->response()->getData(true);
            return $this->sendResponse($response, "Competenies");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
