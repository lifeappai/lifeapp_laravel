<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{

    const TOKEN_URL = 'https://www.universal-tutorial.com/api/getaccesstoken';
    const GET_COUNTRIES = 'https://www.universal-tutorial.com/api/countries';
    const GET_STATES = 'https://www.universal-tutorial.com/api/states/';
    const GET_CITIES = 'https://www.universal-tutorial.com/api/cities/';
    const API_TOKEN = 'wRCmRitRjXYexAOXJagpn5S8fek36kGmYfDvJO3dgb9A3qkGMX3YlBUX4B9N6Qvjizo';
    const TOKEN_EMAIL = 'ceo@magic-lantern.in';


    /**
     * @return JsonResponse
     */
    public function country(): JsonResponse
    {
        if (
            !Cache::has('countries') ||
            !Cache::has('country_cache_time') ||
            Carbon::now()->diffInDays(Cache::get('country_cache_time')) > 2
        ) {
            $token = $this->getToken();
            if ($token) {
                $response = Http::withToken($token)->get(self::GET_COUNTRIES);
                if ($response->ok()) {
                    Cache::forever('countries', $response->json());
                    Cache::forever('country_cache_time', Carbon::now());
                }
            }
        }

        return new JsonResponse(Cache::get('countries'));
    }

    /**
     * @param string $country_name
     *
     * @return JsonResponse
     */
    public function states(string $country_name): JsonResponse
    {
        $country_name = strtolower($country_name);
        $data = json_decode(file_get_contents(storage_path("$country_name.json")), true);

        return new JsonResponse($data);
    }

    /**
     * @param string $state_name
     *
     * @return JsonResponse
     */
    public function cities(string $state_name): JsonResponse
    {
        $data = json_decode(file_get_contents(storage_path("india.json")), true);

        $states = collect($data)->where('state_name', $state_name)->first();

        return new JsonResponse($states["cities"]);
    }

    /**
     * @return string|null
     */
    private function getToken(): ?string
    {
        if (!Cache::has('universal-tutorial_auth_token')) {
            $response = Http::withHeaders(
                [
                    "api-token" => self::API_TOKEN,
                    "user-email" => self::TOKEN_EMAIL
                ]
            )->get(self::TOKEN_URL);

            if ($response->ok()) {
                Cache::put('universal-tutorial_auth_token', $response->json('auth_token'), now()->addHours(22));
            } else {
                Log::critical($response->body());
                return null;
            }
        }

        return Cache::get('universal-tutorial_auth_token');

    }
}
