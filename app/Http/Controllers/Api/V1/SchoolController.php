<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        $schools = School::select('id','name')->with(['students' => function($query) {
            $query->select('school_id','mobile_no');
            $query->where('is_register', 0);
        }])->get();
        return new JsonResponse(['schools' => $schools],Response::HTTP_OK);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id) : JsonResponse
    {
        $schools = School::find($id);
        return new JsonResponse(['schools' => $schools],Response::HTTP_OK);
    }
}
