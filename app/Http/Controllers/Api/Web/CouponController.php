<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CouponResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Coupon;
use Illuminate\Support\Facades\Storage;


class CouponController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        return new JsonResponse(['coupons' => CouponResource::collection(Coupon::get())],Response::HTTP_OK);
    }
    /**
     * @return JsonResponse
     */
    public function createCoupon(Request $request) : JsonResponse
    {
        $data = $request->validate(
			[
				"title"         => ['required', 'string'],
				'category_id'   => ['required', 'integer'],
                'coin'          => ['required', 'integer'],
                'details'       => ['required', 'string'],
				'coupon_image'  => ['required', 'mimes:jpeg,png,jpg'],
                'link' => ['string'],
			]
		);
        $image = $data['coupon_image'];

        $mediaName = $image->getClientOriginalName();
		$mediaPath = Storage::put('media', $image );
        $media = Media::create(
			[
				'name'    => $mediaName,
				'path'    => $mediaPath
			]
		);

        Coupon::create(
            [
				'title' => $data['title'],
                'category_id' => $data['category_id'],
                'coin' => $data['coin'],
                'details' => $data['details'],
				'coupon_media_id'  => $media->id,
                'link' => $data['link'],
			]
        );
        $response = [
            'message' => "Coupon Create successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

}
