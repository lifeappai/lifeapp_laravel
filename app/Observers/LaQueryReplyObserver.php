<?php

namespace App\Observers;

use App\Models\LaQueryReply;

class LaQueryReplyObserver
{
    /**
     * Handle the LaQueryReply "created" event.
     *
     * @param  \App\Models\LaQueryReply  $laQueryReply
     * @return void
     */
    public function created(LaQueryReply $laQueryReply)
    {
        $laQueryReply->laQuery->waiting_reply = (int)($laQueryReply->laQuery->created_by == $laQueryReply->user_id);
        $laQueryReply->laQuery->save();
    }

    /**
     * Handle the LaQueryReply "updated" event.
     *
     * @param  \App\Models\LaQueryReply  $laQueryReply
     * @return void
     */
    public function updated(LaQueryReply $laQueryReply)
    {
        //
    }

    /**
     * Handle the LaQueryReply "deleted" event.
     *
     * @param  \App\Models\LaQueryReply  $laQueryReply
     * @return void
     */
    public function deleted(LaQueryReply $laQueryReply)
    {
        //
    }

    /**
     * Handle the LaQueryReply "restored" event.
     *
     * @param  \App\Models\LaQueryReply  $laQueryReply
     * @return void
     */
    public function restored(LaQueryReply $laQueryReply)
    {
        //
    }

    /**
     * Handle the LaQueryReply "force deleted" event.
     *
     * @param  \App\Models\LaQueryReply  $laQueryReply
     * @return void
     */
    public function forceDeleted(LaQueryReply $laQueryReply)
    {
        //
    }
}
