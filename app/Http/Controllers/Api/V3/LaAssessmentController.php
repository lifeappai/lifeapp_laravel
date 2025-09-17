<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\StatusEnum;
use App\Http\Resources\API\V3\LaAssessmentResource;
use App\Models\LaAssessment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class LaAssessmentController extends ResponseController
{
    public function index(Request $request)
    {
        try {
            $validate = array(
                'la_subject_id' => ['required', 'exists:la_subjects,id'],
                'la_grade_id' => ['required', 'exists:la_grades,id'],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }
            $laAssessments = LaAssessment::where('status', StatusEnum::ACTIVE)
                ->where('la_grade_id', $request->la_grade_id)
                ->where('la_subject_id', $request->la_subject_id)
                ->paginate(15);
            $response['laAssessments'] =  LaAssessmentResource::collection($laAssessments)->response()->getData(true);
            return $this->sendResponse($response, "Assessments");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
