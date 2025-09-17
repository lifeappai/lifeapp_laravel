<?php

namespace App\Http\Controllers\Api\V2;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V2\LanguageResource;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LanguageController extends ResponseController
{
    public function index()
    {
        try {
            $languages = Language::orderBy('id', 'desc')->where('status', StatusEnum::ACTIVE)->get();
            $response['languages'] = LanguageResource::collection($languages);
            return $this->sendResponse($response, "Languages");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
