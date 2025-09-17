<?php

namespace App\Observers;

use App\Models\CoinTransaction;
use App\Models\UserMissionComplete;

class UserMissionCompleteObserver
{
    /**
     * Handle the UserMissionComplete "created" event.
     *
     * @param  \App\Models\UserMissionComplete  $userMissionComplete
     * @return void
     */
    public function created(UserMissionComplete $userMissionComplete)
    {
        $transaction = new CoinTransaction([
            'user_id' => $userMissionComplete->user_id,
            'amount' => $userMissionComplete->earn_points,
            'type' => $userMissionComplete->mission_type === 'brain' ? CoinTransaction::TYPE_BRAIN : CoinTransaction::TYPE_HEART,
        ]);

        $transaction->attachObject($userMissionComplete);
    }

    /**
     * Handle the UserMissionComplete "updated" event.
     *
     * @param  \App\Models\UserMissionComplete  $userMissionComplete
     * @return void
     */
    public function updated(UserMissionComplete $userMissionComplete)
    {
        $transaction = CoinTransaction::ofObject($userMissionComplete)->first();

        if ($transaction) {
            $transaction->amount = $userMissionComplete->earn_points;
            $transaction->save();
        }
    }

    /**
     * Handle the UserMissionComplete "deleted" event.
     *
     * @param  \App\Models\UserMissionComplete  $userMissionComplete
     * @return void
     */
    public function deleted(UserMissionComplete $userMissionComplete)
    {
        //
    }

    /**
     * Handle the UserMissionComplete "restored" event.
     *
     * @param  \App\Models\UserMissionComplete  $userMissionComplete
     * @return void
     */
    public function restored(UserMissionComplete $userMissionComplete)
    {
        //
    }

    /**
     * Handle the UserMissionComplete "force deleted" event.
     *
     * @param  \App\Models\UserMissionComplete  $userMissionComplete
     * @return void
     */
    public function forceDeleted(UserMissionComplete $userMissionComplete)
    {
        //
    }
}
