<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\V1\CouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\CoinTransaction;
use App\Models\CouponRedeem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;


class CouponController extends Controller
{
    /**
     * @param CouponRequest $request
     * @return JsonResponse
     */
    public function index(CouponRequest $request)
    {
        $data = $request->validated();

        if ($data['category_id'] == 0) {
            return new JsonResponse([
                'coupons' => CouponResource::collection(Coupon::get())
            ], Response::HTTP_OK);
        }

        return new JsonResponse([
            'coupons' => CouponResource::collection(Coupon::where('category_id', '=', $data['category_id'])->get())
        ], Response::HTTP_OK);
    }

    public function redeem(Request $request, Coupon $coupon)
    {
        $user = $request->user();
        if ($coupon->coin > $user->brain_coins) {
            return response()->json([
                'error' => 'Insufficient coins'
            ], 400);
        }

        DB::beginTransaction();
        $redeem = CouponRedeem::create([
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
            'coins' => $coupon->coin,
            'link' => $coupon->link
        ]);

        $transaction = new CoinTransaction([
            "user_id" => $user->id,
            "type" => CoinTransaction::TYPE_COUPON,
            "amount" => -1*$coupon->coin,
        ]);
        $transaction->attachObject($redeem);
        DB::commit();

        return response()->json([
            'message' => 'Coupon redeem successfully.',
            'link' => $coupon->link,
            'brain_coins' => $user->brain_coins,
        ]);
    }

}
