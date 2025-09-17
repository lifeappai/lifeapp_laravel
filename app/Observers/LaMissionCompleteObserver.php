<?php

namespace App\Observers;

use App\Models\CoinTransaction;
use App\Models\LaMissionComplete;

class LaMissionCompleteObserver
{
    /**
     * Handle the LaMissionComplete "created" event.
     *
     * @param  \App\Models\LaMissionComplete  $laMissionComplete
     * @return void
     */
    public function created(LaMissionComplete $laMissionComplete)
    {
    }

    /**
     * Handle the LaMissionComplete "updated" event.
     *
     * @param  \App\Models\LaMissionComplete  $laMissionComplete
     * @return void
     */
    public function updated(LaMissionComplete $laMissionComplete)
    {
    }

    /**
     * Handle the LaMissionComplete "deleted" event.
     *
     * @param  \App\Models\LaMissionComplete  $laMissionComplete
     * @return void
     */
    public function deleted(LaMissionComplete $laMissionComplete)
    {
        //
    }

    /**
     * Handle the LaMissionComplete "restored" event.
     *
     * @param  \App\Models\LaMissionComplete  $laMissionComplete
     * @return void
     */
    public function restored(LaMissionComplete $laMissionComplete)
    {
        //
    }

    /**
     * Handle the LaMissionComplete "force deleted" event.
     *
     * @param  \App\Models\LaMissionComplete  $laMissionComplete
     * @return void
     */
    public function forceDeleted(LaMissionComplete $laMissionComplete)
    {
        //
    }
}
