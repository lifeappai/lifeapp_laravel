<?php

namespace App\Http\Controllers\Api\V3;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class LaCampaignController extends ResponseController
{
    public function getTodayCampaigns()
    {
        try {
            $today = Carbon::today()->toDateString();
            $baseUrl = config('filesystems.disks.spaces.url');

            $campaigns = DB::table('la_campaigns as c')
                ->leftJoin('media as m', 'm.id', '=', 'c.media_id')
                ->leftJoin('la_missions as ms', function ($join) {
                    $join->on('c.reference_id', '=', 'ms.id')->where('c.game_type', 1);
                })
                ->leftJoin('la_topics as tp', function ($join) {
                    $join->on('c.reference_id', '=', 'tp.id')->where('c.game_type', 2);
                })
                ->leftJoin('visions as v', function ($join) {
                    $join->on('c.reference_id', '=', 'v.id')->where('c.game_type', 7);
                })
                ->whereDate('c.scheduled_for', '<=', $today)
                ->where('c.status', 1)   // active campaigns only
                ->select([
                    'c.id',
                    'c.title',
                    'c.description',
                    'c.button_name',
                    'c.game_type',
                    'c.reference_id',
                    'c.scheduled_for',
                    DB::raw("COALESCE(ms.la_subject_id, tp.la_subject_id, v.la_subject_id) as la_subject_id"),
                    DB::raw("COALESCE(ms.la_level_id, tp.la_level_id, v.la_level_id) as la_level_id"),
                    DB::raw("COALESCE(
                        JSON_UNQUOTE(JSON_EXTRACT(ms.title, '$.en')),
                        JSON_UNQUOTE(JSON_EXTRACT(tp.title, '$.en')),
                        JSON_UNQUOTE(JSON_EXTRACT(v.title, '$.en'))
                    ) as reference_title"),
                    'm.path as image_path'
                ])
                ->orderBy('c.scheduled_for', 'desc')
                ->get();

            $campaigns->transform(function ($item) use ($baseUrl) {
                $item->image_url = $item->image_path ? $baseUrl . '/' . ltrim($item->image_path, '/') : null;
                unset($item->image_path);
                return $item;
            });

            return response()->json([
                'campaigns' => $campaigns,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
