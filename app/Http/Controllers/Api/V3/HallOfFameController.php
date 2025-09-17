<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Resources\API\V2\HallOfFameResource;
use App\Http\Resources\PublicUserResrouce;
use App\Models\CoinTransaction;
use App\Models\LaMissionComplete;
use App\Models\LaQuizGameResult;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class HallOfFameController extends ResponseController
{
    public function index(Request $request)
    {
        try {
            $state = $request->state;
            $city = $request->city;
            $quizChampion = LaQuizGameResult::orderBy('coins', 'desc');
            if ($state) {
                $quizChampion = $quizChampion->whereHas('user', function ($query) use ($state) {
                    $query->where('state', $state);
                });
            }
            if ($city) {
                $quizChampion = $quizChampion->whereHas('user', function ($query) use ($city) {
                    $query->where('city', $city);
                });
            }
            $quizChampion = $quizChampion->first();
            $quizChampionData['coins'] = $quizChampion ? $quizChampion->coins : 0;
            $quizChampionData['user'] = $quizChampion ? ($quizChampion->user ? new PublicUserResrouce($quizChampion->user) : null) : null;
            $response['quiz_champion'] = $quizChampion ? new HallOfFameResource($quizChampionData) : null;

            $missionChampion = LaMissionComplete::whereNotNull('approved_at')->orderBy('points', 'desc');

            if ($state) {
                $missionChampion = $missionChampion->whereHas('user', function ($query) use ($state) {
                    $query->where('state', $state);
                });
            }
            if ($city) {
                $missionChampion = $missionChampion->whereHas('user', function ($query) use ($city) {
                    $query->where('city', $city);
                });
            }
            $missionChampion = $missionChampion->first();
            $missionChampionData['coins'] = $missionChampion ? $missionChampion->points : 0;
            $missionChampionData['user'] = $missionChampion ? ($missionChampion->user ? new PublicUserResrouce($missionChampion->user) : null) : null;
            $response['mission_champion'] = $missionChampion ? new HallOfFameResource($missionChampionData) : null;

            $coinChampion = CoinTransaction::where('amount', '>', 0)->select('id', 'user_id', DB::raw("SUM(amount) as coin_amount"))->groupBy('user_id')->orderBy('coin_amount', 'desc');

            if ($state) {
                $coinChampion = $coinChampion->whereHas('user', function ($query) use ($state) {
                    $query->where('state', $state);
                });
            }
            if ($city) {
                $coinChampion = $coinChampion->whereHas('user', function ($query) use ($city) {
                    $query->where('city', $city);
                });
            }
            $coinChampion = $coinChampion->first();

            $coinChampionData['coins'] = $coinChampion ? $coinChampion->coin_amount : 0;
            $coinChampionData['user'] = $coinChampion ? ($coinChampion->user ? new PublicUserResrouce($coinChampion->user) : null) : null;
            $response['coin_champion'] = $coinChampion ? new HallOfFameResource($coinChampionData) : null;

            return $this->sendResponse($response, "Hall Of Fame Data");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
