<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\LessionPlanCategoryEnum;
use App\Enums\StatusEnum;
use App\Http\Resources\API\V3\LaLessionPlanResource;
use App\Models\LaLessionPlan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LaLessionPlanController extends ResponseController
{
    public function index(Request $request)
    {
        try {
            $validate = array(
                'type' => ['required', Rule::in(LessionPlanCategoryEnum::Category)],
                'la_board_id' => ['nullable', 'exists:la_boards,id'],
                'la_lession_plan_language_id' => ['required', 'exists:la_lession_plan_languages,id'],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $laLessionPlans = LaLessionPlan::orderBy('id', 'desc')->where('status', StatusEnum::ACTIVE)->where('la_lession_plan_language_id', $request->la_lession_plan_language_id)->where('type', $request->type);
            if($request->la_board_id){
                // $laLessionPlans =  $laLessionPlans->where('la_board_id', $request->la_board_id);
                $laLessionPlans = $laLessionPlans->where(function ($query) use ($request) {
                    $query->where('la_board_id', $request->la_board_id)
                          ->orWhereNull('la_board_id');
                });
            }
            $laLessionPlans = $laLessionPlans->get();
            $response['laLessionPlans'] =  LaLessionPlanResource::collection($laLessionPlans);
            return $this->sendResponse($response, "Lession Plans");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}