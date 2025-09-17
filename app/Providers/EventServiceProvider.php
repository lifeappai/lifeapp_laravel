<?php

namespace App\Providers;

use App\Models\CoinTransaction;
use App\Models\LaMissionComplete;
use App\Models\LaQueryReply;
use App\Models\MovieUserTiming;
use App\Models\PushNotificationCampaign;
use App\Models\QuestionAttempt;
use App\Models\UserMissionComplete;
use App\Observers\CoinTransactionObserver;
use App\Observers\LaMissionCompleteObserver;
use App\Observers\LaQueryReplyObserver;
use App\Observers\MovieUserTimeObserver;
use App\Observers\PushNotificationCampaignObserver;
use App\Observers\QuestionAttemptObserver;
use App\Observers\UserMissionCompleteObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        QuestionAttempt::observe(QuestionAttemptObserver::class);
        MovieUserTiming::observe(MovieUserTimeObserver::class);
        UserMissionComplete::observe(UserMissionCompleteObserver::class);
        CoinTransaction::observe(CoinTransactionObserver::class);
        LaMissionComplete::observe(LaMissionCompleteObserver::class);

        PushNotificationCampaign::observe(PushNotificationCampaignObserver::class);
        LaQueryReply::observe(LaQueryReplyObserver::class);
    }
}
