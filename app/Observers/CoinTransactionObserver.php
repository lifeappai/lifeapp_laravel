<?php

namespace App\Observers;

use App\Models\CoinTransaction;
use Illuminate\Support\Facades\Log;

class CoinTransactionObserver
{
    /**
     * Handle the CoinTransaction "created" event.
     *
     * @param CoinTransaction $coinTransaction
     * @return void
     */
    public function created(CoinTransaction $coinTransaction)
    {
        if ($coinTransaction->type == CoinTransaction::TYPE_HEART) {
            $coinTransaction->user->heart_coins = $coinTransaction->user->heart_coins + $coinTransaction->amount;
        } elseif ($coinTransaction->type == CoinTransaction::TYPE_BRAIN) {
            $coinTransaction->user->brain_coins = $coinTransaction->user->brain_coins + $coinTransaction->amount;
        } elseif ($coinTransaction->type == CoinTransaction::TYPE_QUIZ ||
            $coinTransaction->type == CoinTransaction::TYPE_COUPON ||
            $coinTransaction->type == CoinTransaction::TYPE_MISSION ||
            $coinTransaction->type == CoinTransaction::TYPE_ADMIN ||
            $coinTransaction->type == CoinTransaction::TYPE_VISION ||
            $coinTransaction->type == CoinTransaction::TYPE_ASSIGN_TASK ||
            $coinTransaction->type == CoinTransaction::TYPE_CORRECT_SUBMISSION) {

            $coinTransaction->user->earn_coins = $coinTransaction->user->earn_coins + $coinTransaction->amount;
        }

        $coinTransaction->user->save();
    }

    /**
     * Handle the CoinTransaction "updated" event.
     *
     * @param CoinTransaction $coinTransaction
     * @return void
     */
    public function updating(CoinTransaction $coinTransaction)
    {
        $data = $coinTransaction->getDirty();

        if (isset($data['amount'])) {
            if ($coinTransaction->type == CoinTransaction::TYPE_HEART) {
                $coinTransaction->user->heart_coins =
                    $coinTransaction->user->heart_coins -
                    $coinTransaction->getOriginal('amount') +
                    $data['amount'];
            } elseif ($coinTransaction->type == CoinTransaction::TYPE_BRAIN) {
                $coinTransaction->user->brain_coins =
                    $coinTransaction->user->brain_coins -
                    $coinTransaction->getOriginal('amount') +
                    $data['amount'];
            } elseif ($coinTransaction->type == CoinTransaction::TYPE_QUIZ || $coinTransaction->type == CoinTransaction::TYPE_COUPON || $coinTransaction->type == CoinTransaction::TYPE_MISSION || $coinTransaction->type == CoinTransaction::TYPE_VISION || $coinTransaction->type == CoinTransaction::TYPE_ASSIGN_TASK || $coinTransaction->type == CoinTransaction::TYPE_CORRECT_SUBMISSION) {
                $coinTransaction->user->earn_coins =
                    $coinTransaction->user->earn_coins -
                    $coinTransaction->getOriginal('amount') +
                    $data['amount'];
            }
            $coinTransaction->user->save();
        }
    }
}
