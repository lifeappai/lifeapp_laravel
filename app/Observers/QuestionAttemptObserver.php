<?php

namespace App\Observers;

use App\Models\CoinTransaction;
use App\Models\QuestionAttempt;

class QuestionAttemptObserver
{
    /**
     * Handle the QuestionAttempt "created" event.
     *
     * @param  \App\Models\QuestionAttempt  $questionAttempt
     * @return void
     */
    public function created(QuestionAttempt $questionAttempt)
    {
        $transaction = new CoinTransaction([
            'user_id' => $questionAttempt->user_id,
            'amount' => $questionAttempt->earn_point,
            'type' => $questionAttempt->point_type === 'brain' ? CoinTransaction::TYPE_BRAIN : CoinTransaction::TYPE_HEART,
        ]);

        $transaction->attachObject($questionAttempt);
    }

    /**
     * Handle the QuestionAttempt "updated" event.
     *
     * @param  \App\Models\QuestionAttempt  $questionAttempt
     * @return void
     */
    public function updated(QuestionAttempt $questionAttempt)
    {
        $transaction = CoinTransaction::ofObject($questionAttempt)->first();

        if ($transaction) {
            $transaction->amount = $questionAttempt->earn_point;
            $transaction->save();
        }
    }

    /**
     * Handle the QuestionAttempt "deleted" event.
     *
     * @param  \App\Models\QuestionAttempt  $questionAttempt
     * @return void
     */
    public function deleted(QuestionAttempt $questionAttempt)
    {
        //
    }

    /**
     * Handle the QuestionAttempt "restored" event.
     *
     * @param  \App\Models\QuestionAttempt  $questionAttempt
     * @return void
     */
    public function restored(QuestionAttempt $questionAttempt)
    {
        //
    }

    /**
     * Handle the QuestionAttempt "force deleted" event.
     *
     * @param  \App\Models\QuestionAttempt  $questionAttempt
     * @return void
     */
    public function forceDeleted(QuestionAttempt $questionAttempt)
    {
        //
    }
}
