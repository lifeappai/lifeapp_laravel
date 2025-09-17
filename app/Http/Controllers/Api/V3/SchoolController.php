<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\SchoolResource;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SchoolController extends ResponseController
{
    public function index(Request $request)
    {
        try {
            $name = $request->name;
            $schools = School::orderBy('name', 'asc')->where('status', StatusEnum::ACTIVE)->where('app_visible', StatusEnum::ACTIVE);
            if ($name) {
                $schools = $schools->where('name', 'LIKE', '%' . $name . '%');
            }
            if ($request->code) {
                $schools = $schools->where('code', $request->code);
            }
            $schools = $schools->take(20)->latest()->get();

            $response['school'] = SchoolResource::collection($schools);
            return $this->sendResponse($response, "Schools");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function show(School $school)
    {
        try {
            $response['school'] = new SchoolResource($school);
            return $this->sendResponse($response, "School Details");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function verifySchoolCode(Request $request)
    {
        try {
            $request->validate([
                'code' => ['required'],
            ]);
            $school = School::where('code', $request->code)->first();
            if ($school) {
                $response['school'] = new SchoolResource($school);
                return $this->sendResponse($response, "School Details");
            }
            return $this->sendError("No School found");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
