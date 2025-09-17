<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Category;



class CategoryController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        return new JsonResponse(['category' => CategoryResource::collection(Category::get())],Response::HTTP_OK);
    }
    /**
     * @return JsonResponse
     */
    public function createCategory(Request $request) : JsonResponse
    {
        $data = $request->validate(
			[
				"title" => ['required', 'string'],
			]
		);
       
         Category::create(
            [
				'title' => $data['title'],
			]
        );
        $response = [
            'message' => "Category create successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);  
    }
   
}
