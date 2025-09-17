<?php

namespace App\Http\Traits;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use App\Constants\RatingMultiplier;

trait CoinTrait
{
    public function coinCalculation(int $point, int $rating)
    {
        try {
            $earnPoints = RatingMultiplier::ATTEMPT_RATING[$rating] * $point;
            return $earnPoints;
        } catch (\Exception $e) {
            Log::channel('coin')->critical($e->getMessage());
            throw $e;
        }
    }
}
