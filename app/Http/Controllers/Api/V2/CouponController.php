<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\V1\CouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\CoinTransaction;
use App\Models\Coupon;
use App\Models\CouponRedeem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CouponController extends ResponseController
{
    /**
     * @param CouponRequest $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $validate = array(
                'category_id' => ['integer'],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $coupons = Coupon::orderBy('index');
            if ($request->category_id) {
                if ($request->category_id != 0) {
                    $coupons = $coupons->where('category_id', '=', $request->category_id);
                }
            }
            $coupons = $coupons->get();
            $data = CouponResource::collection($coupons);
            return $this->sendResponse($data, "Coupons");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function redeem(Request $request, Coupon $coupon)
    {
        $user = $request->user();
        if ($coupon->coin > $user->earn_coins) {
            return response()->json([
                'error' => 'Insufficient coins'
            ], 400);
        }

        $checkRedeem = CouponRedeem::where('user_id', $user->id)->where('coupon_id', $coupon->id)->first();
        if ($checkRedeem) {
            return $this->sendError("This Coupon Already Redeemed");
        }
        $redeem = CouponRedeem::create([
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
            'coins' => $coupon->coin,
            'link' => $coupon->link
        ]);

        $transaction = new CoinTransaction([
            "user_id" => $user->id,
            "type" => CoinTransaction::TYPE_COUPON,
            "amount" => -1 * $coupon->coin,
        ]);

        $transaction->attachObject($redeem);

        $user = User::find($user->id);
        $response['link'] = $coupon->link;
        $response['coins'] = $user->earn_coins;
        return $this->sendResponse($response, "Coupon redeem successfully");
    }
}
