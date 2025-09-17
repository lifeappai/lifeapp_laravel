<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\V1\CouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\CoinTransaction;
use App\Models\Coupon;
use App\Models\CouponRedeem;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\UserType;

class CouponController extends ResponseController
{
    /**
     * @param CouponRequest $request
     * @return mixed
     */
    public function index(Request $request)
    {
        try {
            $validate = [
                'category_id' => ['integer'],
            ];

            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $user = $request->user();

            // ✅ Check school code eligibility
            $eligibleForRedeem = School::where('id', $user->school_id)
                ->whereNotNull('code')
                ->exists();

            // ✅ Get student budget settings
            $coinPerRupee = (int) DB::table('app_settings')->where('key', 'coin_per_rupee')->value('value') ?? 4;
            $budgetRupees = (int) DB::table('app_settings')->where('key', 'redeem_budget_rupees_student')->value('value') ?? 5000;
            $budgetCoins = $budgetRupees * $coinPerRupee;

            // ✅ Calculate redeemed coins this month
            $totalRedeemedCoins = CouponRedeem::whereHas('user', function ($q) {
                    $q->where('type', UserType::Student);
                })
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('coins');

            $budgetExceeded = $totalRedeemedCoins >= $budgetCoins;

            // ✅ Fetch coupons only if eligible
            $coupons = collect();
            
            $coupons = Coupon::orderBy('index')
                ->where('type', 1) // student type
                ->where('status', 1);

            if ($request->category_id && $request->category_id != 0) {
                $coupons = $coupons->where('category_id', $request->category_id);
            }

            $coupons = $coupons->get();
            
            return response()->json([
                'success' => true,
                'school_code' => $eligibleForRedeem,
                'budget_exceeded' => $budgetExceeded,
                'data' => CouponResource::collection($coupons),
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }


    public function redeem(Request $request, Coupon $coupon)
    {
        $user = $request->user();

        // Already redeemed check
        $checkRedeem = CouponRedeem::where('user_id', $user->id)
            ->where('coupon_id', $coupon->id)
            ->first();

        if ($checkRedeem) {
            return $this->sendError("This Coupon Already Redeemed");
        }

        // Check coins
        if ($coupon->coin > $user->earn_coins) {
            return response()->json([
                'error' => 'Insufficient coins'
            ], 400);
        }

        // Proceed with redeem
        $redeem = CouponRedeem::create([
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
            'coins' => $coupon->coin,
            'link' => $coupon->link,
            'status' => 'Processing',
            'status_updated_at' => now(),
        ]);

        // Log coin transaction
        $transaction = new CoinTransaction([
            "user_id" => $user->id,
            "type" => CoinTransaction::TYPE_COUPON,
            "amount" => -1 * $coupon->coin,
        ]);

        $transaction->attachObject($redeem);

        // Refresh user balance
        $user = User::find($user->id);

        return $this->sendResponse([
            'link' => $coupon->link,
            'coins' => $user->earn_coins,
            'status' => $redeem->status,
        ], "Coupon redeemed successfully");
    }

    // Teacher shop APIs
    public function teacherIndex(Request $request)
    {
        try {
            $validate = [
                'category_id' => ['integer'],
            ];
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $teacher = $request->user();

            // ✅ Check school code eligibility
            $eligibleForRedeem = School::where('id', $teacher->school_id)
                ->whereNotNull('code')
                ->exists();

            // ✅ Fetch coin values
            $available_coins = $teacher->earn_coins;

            $total_earned = \App\Models\CoinTransaction::where('user_id', $teacher->id)
                ->whereIn('type', [
                    \App\Models\CoinTransaction::TYPE_ASSIGN_TASK,
                    \App\Models\CoinTransaction::TYPE_CORRECT_SUBMISSION,
                    \App\Models\CoinTransaction::TYPE_MISSION,
                    \App\Models\CoinTransaction::TYPE_VISION,
                    \App\Models\CoinTransaction::TYPE_QUIZ,
                    \App\Models\CoinTransaction::TYPE_ADMIN,
                ])
                ->sum('amount');

            // ✅ Budget cap logic
            $coinPerRupee = (int) DB::table('app_settings')->where('key', 'coin_per_rupee')->value('value') ?? 4;
            $budgetRupees = (int) DB::table('app_settings')->where('key', 'redeem_budget_rupees_teacher')->value('value') ?? 5000;
            $budgetCoins = $budgetRupees * $coinPerRupee;

            $totalRedeemedCoins = CouponRedeem::whereHas('user', function ($q) {
                    $q->where('type', UserType::Teacher);
                })
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('coins');

            $budgetExceeded = $totalRedeemedCoins >= $budgetCoins;

            // ✅ Fetch coupons only if eligible
            $coupons = collect();
            
            $coupons = Coupon::orderBy('index')
                ->where('type', 2)
                ->where('status', 1);

            if ($request->category_id && $request->category_id != 0) {
                $coupons = $coupons->where('category_id', $request->category_id);
            }
                $coupons = $coupons->get();            

            return response()->json([
                'success' => true,
                'school_code' => $eligibleForRedeem,
                'budget_exceeded' => $budgetExceeded,
                'data' => [
                    'available_coins' => $available_coins,
                    'total_earned_coins' => $total_earned,
                    'coupons' => CouponResource::collection($coupons)
                ]
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function purchaseHistory(Request $request)
    {
        $user = $request->user(); // Authenticated teacher

        $query = CouponRedeem::with(['coupon' => function ($q) {
            $q->select('id', 'title', 'coin', 'link', 'coupon_media_id')
                ->with('media:id,path');
        }])
        ->where('user_id', $user->id)
        ->latest();

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $redeems = $query->get();

        // ✅ Load school info
        $school = null;
        if (!empty($user->school_id)) {
            $school = \App\Models\School::find($user->school_id);
        }

        // ✅ school address
        $schoolAddress = $user->getSchoolAddress();

        $data = $redeems->map(function ($redeem) use ($schoolAddress) {
            return [
                'id' => $redeem->id,
                'coupon_id' => $redeem->coupon_id,
                'title' => $redeem->coupon->title ?? null,
                'coins_used' => $redeem->coins,
                'link' => $redeem->link,
                'url' => $redeem->coupon->media->path ?? null,
                'redeemed_at' => $redeem->created_at->toDateTimeString(),
                'delivery_address' => $schoolAddress,
                'status' => $redeem->status ?? 'Processing', // ✅ include status
                'status_updated_at' => $redeem->status_updated_at 
                    ? Carbon::parse($redeem->status_updated_at)->toDateTimeString() 
                    : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function coinHistory(Request $request)
    {
        $user = $request->user();

        $transactions = CoinTransaction::with('coinable')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $data = $transactions->map(function ($txn) {
            $sourceTitle = 'Untitled';
            $sourceType = class_basename($txn->coinable_type);
            $rawTitle = null;

            // Special logic for TYPE_CORRECT_SUBMISSION (type = 9)
            if ($txn->type == CoinTransaction::TYPE_CORRECT_SUBMISSION) {
                $answerId = $txn->coinable_id;

                // Check if it's from Mission or Vision by using coinable_type
                switch ($sourceType) {
                    case 'LaMission':
                    case 'LaMissionComplete':
                        $missionComplete = \App\Models\LaMissionComplete::with('laMission')->find($answerId);
                        if ($missionComplete && $missionComplete->laMission) {
                            $rawTitle = $missionComplete->laMission->title ?? null;
                            $sourceType = 'LaMission';
                        }
                        break;

                    case 'Vision':
                    case 'VisionQuestionAnswer':
                        $visionAnswer = \App\Models\VisionQuestionAnswer::with('vision')->find($answerId);
                        if ($visionAnswer && $visionAnswer->vision) {
                            $rawTitle = $visionAnswer->vision->title ?? null;
                            $sourceType = 'Vision';
                        }
                        break;
                }
            }

            // For coupon redemptions (type = 3)
            elseif ($txn->type == CoinTransaction::TYPE_COUPON && $txn->coinable) {
                $coupon = $txn->coinable->coupon ?? $txn->coinable->coupon()->first();
                $rawTitle = $coupon?->name ?? $coupon?->title ?? 'Coupon';
                $sourceType = 'CouponRedeem';
            }

            // All other cases
            else {
                $coinable = $txn->coinable;
                $rawTitle = $coinable?->title ?? $coinable?->name ?? null;
            }

            // Decode raw title
            if (isset($rawTitle)) {
                if (is_string($rawTitle)) {
                    $decoded = json_decode($rawTitle, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $sourceTitle = $decoded['en'] ?? reset($decoded) ?? 'Untitled';
                    } else {
                        $sourceTitle = $rawTitle;
                    }
                } elseif (is_array($rawTitle)) {
                    $sourceTitle = $rawTitle['en'] ?? reset($rawTitle) ?? 'Untitled';
                } else {
                    $sourceTitle = $rawTitle ?? 'Untitled';
                }
            }

            return [
                'amount' => $txn->amount,
                'type' => $txn->type,
                'type_label' => CoinTransaction::typeLabels()[$txn->type] ?? 'Unknown',
                'source_title' => $sourceTitle,
                'source_type' => $sourceType,
                'created_at' => $txn->created_at->format('Y-m-d H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    private function extractTitle($source)
    {
        if (isset($source['title'])) {
            $title = $source['title'];

            // If title is JSON string, decode it
            if (is_string($title) && $this->isJson($title)) {
                $titleArr = json_decode($title, true);
                return $titleArr['en'] ?? reset($titleArr) ?? 'Untitled';
            }

            // If already array (edge case), try to extract
            if (is_array($title)) {
                return $title['en'] ?? reset($title) ?? 'Untitled';
            }

            // Otherwise return string as-is
            return $title;
        }

        return $source['name'] ?? 'N/A';
    }

    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }


}
