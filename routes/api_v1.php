<?php

use App\Http\Controllers\Api\V1\LocationController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\V1\UserController;
use \App\Http\Controllers\Api\V1\SchoolController;
use \App\Http\Controllers\Api\V1\AuthenticationController;
use App\Http\Controllers\Api\V1\OtpController;
use App\Http\Controllers\Api\V1\MissionController;
use App\Http\Controllers\Api\V1\SubjectController;
use App\Http\Controllers\Api\V1\CouponController;
use App\Http\Controllers\Api\V1\CampaignController;
/*
 | ---------------------------------------------------------
 | OTP API Routes
 | ---------------------------------------------------------
 */

Route::prefix('otp')->group(function () {
    Route::post('/send-otp',[OtpController::class,'sendOtp']);
    Route::post('/confirm',[OtpController::class,'confirmOtp']);
    Route::post('/reset',[UserController::class,'ResetPin']);
    Route::post('/resend-otp',[OtpController::class,'resendOtp']);
});

/*
 | ---------------------------------------------------------
 | Authentication API Routes
 | ---------------------------------------------------------
 */

Route::middleware('auth.master')->group(function () {
    Route::post('/list',[UserController::class,'usersList']);
    Route::post('/login',[UserController::class,'login']);
    Route::post('/create-pin', [UserController::class,'createPin']);
    Route::post('/forget-pin', [OtpController::class, 'forgetPin']);
    Route::post('/reset-pin', [OtpController::class, 'resetPin']);

//    Route::post('/sign-up',[AuthenticationController::class,'signUp']);
});

//Route::post('/register',[AuthenticationController::class,'newRegister']);
Route::get('/country', [LocationController::class, 'country']);
Route::get('/states/{country_name}', [LocationController::class, 'states']);
Route::get('/cities/{state_name}', [LocationController::class, 'cities']);


Route::middleware('auth:api')->group(function () {

    Route::post('/friend-requests', [UserController::class, 'sendFriendRequest']);
    Route::patch('/friend-requests/{friendship}/confirm', [UserController::class, 'confirmFriendRequest']);
    Route::patch('/friend-requests/{friendship}/reject', [UserController::class, 'rejectFriendRequest']);
    Route::get('/friend-requests', [UserController::class, 'getFriendRequest']);

    /*
     | ---------------------------------------------------------
     | User API Routes
     | ---------------------------------------------------------
     */
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);

        Route::get('/me', [UserController::class, 'me']);
        Route::post('/check-username', [AuthenticationController::class, 'checkUsername']);
        Route::post('/set-username', [AuthenticationController::class, 'updateUserName']);
        Route::post('/set-pin', [AuthenticationController::class, 'updatePin']);
        Route::post('/upload-media', [AuthenticationController::class, 'updateProfileImage']);
        Route::post('/logout', [UserController::class, 'logout']);
        Route::post('/edit-profile', [AuthenticationController::class, 'updateProfile']);
        Route::post('/hall-of-fame-data', [SubjectController::class, 'hallOfFame']);

        Route::get('{user}/friend-requests', [UserController::class, 'friendRequests']);
        Route::get('{user}/friends', [UserController::class, 'friendList']);
        Route::get('{user}', [UserController::class, 'show']);
    });

    Route::get('/notifications', [UserController::class, 'getNotifications']);

    Route::delete('/notifications', [UserController::class, 'deleteNotifications']);

    Route::post('/clear-notifications', [UserController::class, 'readNotifications']);

    Route::get('/coin-transactions', [UserController::class, 'getCoinTransactions']);


    /*
     | ---------------------------------------------------------
     |  Movies Routes
     | ---------------------------------------------------------
     */

    Route::prefix('movies')->group(function () {
        Route::post('/subjects', [SubjectController::class, 'index']);
        Route::post('/topics', [SubjectController::class, 'getTopics']);
        Route::post('/movie', [SubjectController::class, 'getMovie']);
        Route::post('/quiz', [SubjectController::class, 'getQuiz']);
        Route::post('/update-video-timing', [SubjectController::class, 'updateVideoTiming']);
        Route::post('/update-question-attempt', [SubjectController::class, 'updateQuestionAttempt']);
        Route::post('/movie-completed', [SubjectController::class, 'movieCompleted']);
        Route::post('/user-movie-assessment', [SubjectController::class, 'userMovieAssessment']);
    });

    /*
     | ---------------------------------------------------------
     |  Mission Routes
     | ---------------------------------------------------------
     */

    Route::prefix('missions')->group(function () {

        Route::post('/list', [MissionController::class, 'index']);
        Route::post('/upload-media', [MissionController::class, 'uploadMissionDocument']);
        Route::post('/update-user-timing', [MissionController::class, 'updateMissionUserTiming']);
        Route::post('/mission-complete', [MissionController::class, 'completeMission']);
    });

    /*
    | ---------------------------------------------------------
    |  Coupons Routes
    | ---------------------------------------------------------
    */
    Route::prefix('coupon')->group(function () {
        Route::post('/list', [CouponController::class, 'index']);

        Route::post('/{coupon}/redeem', [CouponController::class, 'redeem']);
    });
    /*
    | ---------------------------------------------------------
    |  Campaign Routes
    | ---------------------------------------------------------
    */

    Route::get('campaigns', [CampaignController::class, 'index']);
    Route::post('campaigns', [CampaignController::class, 'store']);
    Route::put('campaigns/{campaign}', [CampaignController::class, 'update']);
    Route::get('campaigns/{campaign}', [CampaignController::class, 'show']);
    Route::delete('campaigns/{campaign}', [CampaignController::class, 'delete']);
    Route::get('/requesting-campaigns', [CampaignController::class, 'requestingCampaigns']);

    Route::post('/campaigns/{campaign}/coins', [CampaignController::class, 'coinsGiven']);


//    Route::prefix('campaign')->group(function () {
//        Route::get('/list', [CampaignController::class, 'getCampaigns']);
//        Route::post('/create-campaign', [CampaignController::class, 'createCampaign']);
//        Route::get('/requesting-people', [CampaignController::class, 'requestingPeople']);
//    });

    Route::post('/device-token', [UserController::class, 'updateDeviceToken']);
});

/*
 | ---------------------------------------------------------
 | School API Routes
 | ---------------------------------------------------------
 */
Route::prefix('school')->group(function () {
    Route::get('list',[SchoolController::class,'index']);
    Route::get('details/{id}',[SchoolController::class,'show']);
});
