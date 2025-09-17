<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AppVersionController extends Controller
{
    public function getLatestVersion()
    {
        
        $versionInfo = [
            'status' => 'success',
            'data' => [
                'latest_version' => '3.1.0+59',
                'minimum_required_version' => '3.0.0',
                'is_force_update' => true,
                'update_message' => 'Please update to the latest version to access new features and improvements.',
                'play_store_url' => 'https://play.google.com/store/apps/details?id=com.life.lab',
                'app_store_url' => 'https://apps.apple.com/in/app/life-app-learning/id1631792841'
            ]
        ];

        return response()->json($versionInfo);
    }
}
