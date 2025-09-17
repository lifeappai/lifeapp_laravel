<?php

namespace App\Observers;

use App\Models\PushNotificationCampaign;
use Carbon\Carbon;

class PushNotificationCampaignObserver
{
    /**
     * Handle the PushNotificationCampaign "created" event.
     *
     * @param  PushNotificationCampaign  $pushNotificationCampaign
     * @return void
     */
    public function creating(PushNotificationCampaign $pushNotificationCampaign)
    {
        $pushNotificationCampaign->success_users = [];

        $pushNotificationCampaign->failed_users = [];
    }

    /**
     * Handle the PushNotificationCampaign "updated" event.
     *
     * @param  PushNotificationCampaign  $pushNotificationCampaign
     * @return void
     */
    public function saved(PushNotificationCampaign $pushNotificationCampaign)
    {
        if (
            count($pushNotificationCampaign->users) ===
            count($pushNotificationCampaign->success_users) + count($pushNotificationCampaign->failed_users)
        ) {

            $pushNotificationCampaign->completed_at = Carbon::now()->toDateTimeString();
            $pushNotificationCampaign->save();
        }
    }
}
