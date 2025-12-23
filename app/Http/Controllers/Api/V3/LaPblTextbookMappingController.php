<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\StatusEnum;
use App\Http\Resources\API\V3\LaPblTextbookMappingResource;
use App\Models\LaPblTextbookMapping;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\LaTeacherGrade;



class LaPblTextbookMappingController extends ResponseController
{
    public function index(Request $request)
    {
        try {
            $validate = [
                'la_board_id'   => ['nullable', 'exists:la_boards,id'],
                'language_id'   => ['required', 'exists:languages,id'],
                'la_subject_id' => ['nullable', 'exists:la_subjects,id'],
                'la_grade_id'   => ['nullable', 'exists:la_grades,id'],
            ];

            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $query = LaPblTextbookMapping::query()
                ->where('status', StatusEnum::ACTIVE)
                ->where('language_id', $request->language_id)
                ->orderBy('id', 'desc');

            // Board filter (supports common mappings)
            if ($request->la_board_id) {
                $query->where(function ($q) use ($request) {
                    $q->where('la_board_id', $request->la_board_id)
                    ->orWhereNull('la_board_id');
                });
            }

            // Dropdown subject filter
            if ($request->la_subject_id) {
                $query->where('la_subject_id', $request->la_subject_id);
            }

            // Dropdown grade filter
            if ($request->la_grade_id) {
                $query->where('la_grade_id', $request->la_grade_id);
            }

            $mappings = $query->get();

            return $this->sendResponse([
                'pbl_textbook_mappings' => LaPblTextbookMappingResource::collection($mappings)
            ], 'PBL Textbook Mappings');

        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

}
