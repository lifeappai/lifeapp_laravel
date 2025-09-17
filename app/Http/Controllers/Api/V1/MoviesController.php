<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Models\Subjects;
use App\Models\Levels;
use App\Models\Video;
use App\Models\Quiz;
use App\Models\Questions;
use App\Models\Topics;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Resources\SubjectResource;

class MoviesController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        return new JsonResponse(['subjects' => SubjectResource::collection(Subjects::get())],Response::HTTP_OK);
    }


}
