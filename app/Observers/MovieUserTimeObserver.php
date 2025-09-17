<?php

namespace App\Observers;

use App\Models\CoinTransaction;
use App\Models\MovieUserTiming;

class MovieUserTimeObserver
{
    /**
     * Handle the MovieUserTiming "created" event.
     *
     * @param  \App\Models\MovieUserTiming  $movieUserTiming
     * @return void
     */
    public function created(MovieUserTiming $movieUserTiming)
    {
        $transaction = new CoinTransaction([
            'user_id' => $movieUserTiming->user_id,
            'amount' => $movieUserTiming->earn_point,
            'type' => $movieUserTiming->point_type === 'brain' ? CoinTransaction::TYPE_BRAIN : CoinTransaction::TYPE_HEART,
        ]);

        $transaction->attachObject($movieUserTiming);
    }

    /**
     * Handle the MovieUserTiming "updated" event.
     *
     * @param  \App\Models\MovieUserTiming  $movieUserTiming
     * @return void
     */
    public function updated(MovieUserTiming $movieUserTiming)
    {
        $transaction = CoinTransaction::ofObject($movieUserTiming)->first();

        if ($transaction) {
            $transaction->amount = $movieUserTiming->earn_point;
            $transaction->save();
        }
    }

    /**
     * Handle the MovieUserTiming "deleted" event.
     *
     * @param  \App\Models\MovieUserTiming  $movieUserTiming
     * @return void
     */
    public function deleted(MovieUserTiming $movieUserTiming)
    {
        //
    }

    /**
     * Handle the MovieUserTiming "restored" event.
     *
     * @param  \App\Models\MovieUserTiming  $movieUserTiming
     * @return void
     */
    public function restored(MovieUserTiming $movieUserTiming)
    {
        //
    }

    /**
     * Handle the MovieUserTiming "force deleted" event.
     *
     * @param  \App\Models\MovieUserTiming  $movieUserTiming
     * @return void
     */
    public function forceDeleted(MovieUserTiming $movieUserTiming)
    {
        //
    }
}
