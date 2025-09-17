<?php

use App\Http\Controllers\Api\V2\CouponController;
use App\Http\Controllers\Api\V2\FriendController;
use App\Http\Controllers\Api\V2\HallOfFameController;
use App\Http\Controllers\Api\V2\LaLevelController;
use App\Http\Controllers\Api\V2\LaMissionController;
use App\Http\Controllers\Api\V2\LanguageController;
use App\Http\Controllers\Api\V2\LaQueryController;
use App\Http\Controllers\Api\V2\LaQuizGameController;
use App\Http\Controllers\Api\V2\LaSubjectController;
use App\Http\Controllers\Api\V2\LoginController;
use App\Http\Controllers\Api\V2\OtpController;
use App\Http\Controllers\Api\V2\RegisterController;
use App\Http\Controllers\Api\V2\SchoolController;
use App\Http\Controllers\Api\V2\UserController;
use Illuminate\Support\Facades\Route;


Route::prefix('otp')->group(function () {
    Route::post('/send', [OtpController::class, 'sendOtp']);
    Route::post('/verify', [OtpController::class, 'confirmOtp']);
    Route::post('/resend', [OtpController::class, 'resendOtp']);
});

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/admin-login', [LoginController::class, 'adminLogin']);

Route::prefix('schools')->group(function () {
    Route::get('/', [SchoolController::class, 'index']);
    Route::get('{school}', [SchoolController::class, 'show']);
});

Route::prefix('subjects')->group(function () {
    Route::get('/', [LaSubjectController::class, 'index']);
});

Route::prefix('languages')->group(function () {
    Route::get('/', [LanguageController::class, 'index']);
});

Route::middleware('auth:api')->group(function () {

    Route::prefix('new-subjects')->group(function () {
        Route::get('/', [LaSubjectController::class, 'index']);
    });


    Route::get('dashboard', [UserController::class, 'dashboard']);

    Route::get('/coin-history', [UserController::class, 'getCoinTransactions']);
    Route::get('/hall-of-fame', [HallOfFameController::class, 'index']);
    Route::post('subject-coupon-code/{laSubject}', [LaSubjectController::class, 'assignCouponCodeToUser']);

    Route::prefix('profile')->group(function () {
        Route::post('/', [UserController::class, 'updateProfile']);
        Route::post('image', [UserController::class, 'updateProfileImage']);
    });

    Route::post('/mentor-profile', [UserController::class, 'mentorProfile']);

    Route::prefix('mission')->group(function () {
        Route::get('/', [LaMissionController::class, 'index']);
        Route::get('submission', [LaMissionController::class, 'userMissionSubmissions']);
        Route::get('/{laMission}', [LaMissionController::class, 'show']);
        Route::post('user-timing', [LaMissionController::class, 'updateMissionUserTiming']);
        Route::post('complete', [LaMissionController::class, 'completeMission']);
    });

    Route::get('/users', [UserController::class, 'getUsers']);
    Route::get('/friends', [FriendController::class, 'getFriends']);
    Route::delete('/friends/{user}', [FriendController::class, 'deleteFriend']);
    Route::get('/levels', [LaLevelController::class, 'index']);

    Route::prefix('friend-requests')->group(function () {
        Route::get('/', [FriendController::class, 'friendRequests']);
        Route::get('/invite', [FriendController::class, 'getInviteRequests']);
        Route::post('/send', [FriendController::class, 'sendFriendRequest']);
        Route::patch('/{friendship}/accept', [FriendController::class, 'acceptFriendRequest']);
        Route::patch('/{friendship}/reject', [FriendController::class, 'rejectFriendRequest']);
    });

    Route::prefix('quiz-games')->group(function () {
        Route::post('/', [LaQuizGameController::class, 'createQuiz']);
        Route::get('/{laQuizGame}/participants', [LaQuizGameController::class, 'getQuizGameParticipants']);
        Route::put('/{laQuizGame}/participants', [LaQuizGameController::class, 'changeQuizGameParticipantUser']);
        Route::post('/{laQuizGame}/questions', [LaQuizGameController::class, 'startQuizGame']);
        Route::post('/{laQuizGame}/answers', [LaQuizGameController::class, 'endQuizGame']);
        Route::get('/{laQuizGame}/answers', [LaQuizGameController::class, 'quizGameAnswers']);

        Route::get('/{laQuizGame}', [LaQuizGameController::class, 'show']);

        Route::get('/{laQuizGame}/results', [LaQuizGameController::class, 'getQuizGameResult']);
        Route::get('/', [LaQuizGameController::class, 'quizGameHistory']);
    });

    Route::prefix('queries')->group(function () {

        Route::post('/', [LaQueryController::class, 'store']);
        Route::get('/', [LaQueryController::class, 'index']);

        Route::post('/{laQuery}/assign', [LaQueryController::class, 'assign']);
        Route::post('/{laQuery}/replies', [LaQueryController::class, 'reply']);
        Route::get('/{laQuery}/replies', [LaQueryController::class, 'getReplies']);

        Route::patch('/{laQuery}/close', [LaQueryController::class, 'closeQuery']);
        Route::post('/{laQuery}/feedback', [LaQueryController::class, 'feedbackQuery']);
    });

    Route::prefix('coupon')->group(function () {
        Route::post('/list', [CouponController::class, 'index']);
        Route::post('/{coupon}/redeem', [CouponController::class, 'redeem']);
    });

    Route::get('/notifications', [UserController::class, 'getNotifications']);

    Route::delete('/notifications', [UserController::class, 'deleteNotifications']);

    Route::post('/clear-notifications', [UserController::class, 'readNotifications']);


    Route::prefix('notifications')->group(function () {
        Route::get('/', [UserController::class, 'getNotifications']);
        Route::delete('/', [UserController::class, 'deleteNotifications']);
        Route::get('/clear', [UserController::class, 'readNotifications']);
    });
});
