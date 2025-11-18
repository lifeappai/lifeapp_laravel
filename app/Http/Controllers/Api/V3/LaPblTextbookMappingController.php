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

            $teacherId = Auth::id();

            // 🔑 fetch subject-grade pairs for the teacher
            $teacherGrades = LaTeacherGrade::with(['laSubject', 'laGrade'])
                ->where('user_id', $teacherId)
                ->get();

            $subjectGradePairs = $teacherGrades
                ->filter(fn($tg) => !empty($tg->laSubject) && !empty($tg->laGrade))
                ->map(fn($tg) => [
                    'la_subject_id' => $tg->laSubject->id,
                    'la_grade_id'   => $tg->laGrade->id,
                ])
                ->unique(fn($item) => $item['la_subject_id'].'-'.$item['la_grade_id'])
                ->values();

            if ($subjectGradePairs->isEmpty()) {
                return $this->sendResponse(['pbl_textbook_mappings' => []], "No mappings found for this teacher.");
            }

            // 🔑 build query
            $query = LaPblTextbookMapping::orderBy('id', 'desc')
                ->where('status', StatusEnum::ACTIVE)
                ->where('language_id', $request->language_id);

            if ($request->la_board_id) {
                $query->where(function ($q) use ($request) {
                    $q->where('la_board_id', $request->la_board_id)
                    ->orWhereNull('la_board_id');
                });
            }

            // ✅ filter by teacher's subject-grade pairs
            $query->where(function ($q) use ($subjectGradePairs) {
                foreach ($subjectGradePairs as $pair) {
                    $q->orWhere(function ($subQuery) use ($pair) {
                        $subQuery->where('la_subject_id', $pair['la_subject_id'])
                                ->where('la_grade_id', $pair['la_grade_id']);
                    });
                }
            });

            // Optional: if request has explicit subject/grade filters, narrow further
            if ($request->la_subject_id) {
                $query->where('la_subject_id', $request->la_subject_id);
            }
            if ($request->la_grade_id) {
                $query->where('la_grade_id', $request->la_grade_id);
            }

            $mappings = $query->get();

            $response['pbl_textbook_mappings'] = LaPblTextbookMappingResource::collection($mappings);

            return $this->sendResponse($response, "PBL Textbook Mappings");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

}
