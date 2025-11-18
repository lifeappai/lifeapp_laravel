<?php

use App\Http\Controllers\Api\V3\CouponController;
use App\Http\Controllers\Api\V3\FriendController;
use App\Http\Controllers\Api\V3\GameReportController;
use App\Http\Controllers\Api\V3\HallOfFameController;
use App\Http\Controllers\Api\V3\LaAssessmentController;
use App\Http\Controllers\Api\V3\LaBoardController;
use App\Http\Controllers\Api\V3\LaCompetencyController;
use App\Http\Controllers\Api\V3\LaConceptCartoonController;
use App\Http\Controllers\Api\V3\LaGameEnrollmentController;
use App\Http\Controllers\Api\V3\LaGradeController;
use App\Http\Controllers\Api\V3\LaLessionPlanController;
use App\Http\Controllers\Api\V3\LaLessionPlanLanguageController;
use App\Http\Controllers\Api\V3\LaLevelController;
use App\Http\Controllers\Api\V3\LaMissionController;
use App\Http\Controllers\Api\V3\LanguageController;
use App\Http\Controllers\Api\V3\LaQueryController;
use App\Http\Controllers\Api\V3\LaQuizGameController;
use App\Http\Controllers\Api\V3\LaRequestGameEnrollmentController;
use App\Http\Controllers\Api\V3\LaSectionController;
use App\Http\Controllers\Api\V3\LaSubjectController;
use App\Http\Controllers\Api\V3\LoginController;
use App\Http\Controllers\Api\V3\OtpController;
use App\Http\Controllers\Api\V3\RegisterController;
use App\Http\Controllers\Api\V3\SchoolController;
use App\Http\Controllers\Api\V3\UserController;
use App\Http\Controllers\Api\V3\LaSessionController;
use App\Http\Controllers\Api\V3\LaTeacherController;
use App\Http\Controllers\Api\V3\LaTopicController;
use App\Http\Controllers\Api\V3\LaTrackingReportController;
use App\Http\Controllers\Api\V3\LaWorkSheetController;
use App\Http\Controllers\Api\V3\LaVisionController;
use App\Http\Controllers\Api\V3\LaCampaignController;
use App\Http\Controllers\Api\V3\LaLeaderboardController;
use App\Http\Controllers\Api\V3\AdminNotificationController;
use App\Http\Controllers\Api\V3\LaPblTextbookMappingController;
use App\Http\Controllers\Api\V3\FaqController;
use App\Http\Controllers\Api\V3\ChapterController;
use App\Http\Controllers\TestingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V3\SchoolRawController;
use App\Http\Controllers\Api\V3\QrRedirectController;


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

Route::post('school/code-verify', [SchoolController::class, 'verifySchoolCode']);

Route::get('pending-redirect', [QrRedirectController::class, 'getPendingRedirect']);

Route::prefix('subjects')->group(function () {
    Route::get('/', [LaSubjectController::class, 'index']);
});

Route::prefix('languages')->group(function () {
    Route::get('/', [LanguageController::class, 'index']);
});

Route::get('/sections', [LaSectionController::class, 'index']);
Route::get('/boards', [LaBoardController::class, 'index']);
Route::get('/grades', [LaGradeController::class, 'index']); 

Route::post('/visions/{vision}/notify', [LaVisionController::class, 'notifyVisionStatus']);

Route::post('/missions/{missionId}/notify', [LaMissionController::class, 'notifyMissionStatus']);

Route::post('/admin/send-notification', [AdminNotificationController::class, 'send']);

Route::get('/schoolsraw/search', [SchoolRawController::class, 'search']);

Route::middleware('auth:api')->group(function () {
    Route::prefix('new-subjects')->group(function () {
        Route::get('/', [LaSubjectController::class, 'index']);
    });


    Route::get('dashboard', [UserController::class, 'dashboard']);

    Route::get('/coin-history', [UserController::class, 'getCoinTransactions']);
    Route::get('/hall-of-fame', [HallOfFameController::class, 'index']);
    Route::post('subject-coupon-code/{laSubject}', [LaSubjectController::class, 'assignCouponCodeToUser']);
    Route::post('game-enrollments', [LaGameEnrollmentController::class, 'assignGameEnrollmentToUser']);
    Route::get('check-game-enrollments', [LaGameEnrollmentController::class, 'checkGameEnrollments']);
    Route::post('request-game-enrollment', [LaRequestGameEnrollmentController::class, 'requestGameEnrollment']);

    Route::prefix('profile')->group(function () {
        Route::post('/', [UserController::class, 'updateProfile']);
        Route::post('image', [UserController::class, 'updateProfileImage']);
        Route::post('/teacher', [UserController::class, 'updateTeacherProfile']);
    });

    Route::post('/mentor-profile', [UserController::class, 'mentorProfile']);

    Route::prefix('mission')->group(function () {
        Route::post('/', [LaMissionController::class, 'index']);
        Route::get('submission', [LaMissionController::class, 'userMissionSubmissions']);
        Route::get('/{laMission}', [LaMissionController::class, 'show']);
        Route::post('user-timing', [LaMissionController::class, 'updateMissionUserTiming']);
        Route::post('complete', [LaMissionController::class, 'completeMission']);
        Route::post('/{lamissionId}/skip', [LaMissionController::class, 'skipMission']);
    });

    Route::prefix('vision')->group(function () {
        Route::get('/list', [LaVisionController::class, 'index']);
        Route::get('/{id}', [LaVisionController::class, 'show']);
        Route::get('/{id}/questions', [LaVisionController::class, 'getVisionQuestions']);
        Route::post('/complete', [LaVisionController::class, 'completeVision']);
        Route::post('/result', [LaVisionController::class, 'getVisionResult']);
        Route::post('/update-user-status', [LaVisionController::class, 'updateUserStatus']);
        Route::post('/skip', [LaVisionController::class, 'skipVision']);
        Route::post('/pending', [LaVisionController::class, 'markVisionPending']);
        Route::post('/answers', [LaVisionController::class, 'viewVisionAnswers']);
    });

    Route::get('/users', [UserController::class, 'getUsers']);
    Route::get('/friends', [FriendController::class, 'getFriends']);
    Route::delete('/friends/{user}', [FriendController::class, 'deleteFriend']);
    Route::get('/levels', [LaLevelController::class, 'index']);
    Route::post('/topics', [LaTopicController::class, 'index']);
    Route::post('/competencies', [LaCompetencyController::class, 'index']);
    Route::post('/assessments', [LaAssessmentController::class, 'index']);
    Route::get('/concept-cartoon-header', [LaConceptCartoonController::class, 'headers']);
    Route::post('/concept-cartoons', [LaConceptCartoonController::class, 'index']);
    Route::post('/work-sheets', [LaWorkSheetController::class, 'index']);
    Route::get('/lession-plan-languages', [LaLessionPlanLanguageController::class, 'index']);
    Route::post('/lession-plans', [LaLessionPlanController::class, 'index']);
    Route::post('/pbl-textbook-mappings', [LaPblTextbookMappingController::class, 'index']);

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
        Route::post('/{laQuizGame}/participants', [LaQuizGameController::class, 'addQuizGameParticipants']);
        Route::put('/{laQuizGame}/participants', [LaQuizGameController::class, 'changeQuizGameParticipantUser']);
        Route::post('/{laQuizGame}/questions', [LaQuizGameController::class, 'startQuizGame']);
        Route::post('/{laQuizGame}/answers', [LaQuizGameController::class, 'endQuizGame']);
        Route::get('/{laQuizGame}/answers', [LaQuizGameController::class, 'quizGameAnswers']);

        Route::get('/{laQuizGame}', [LaQuizGameController::class, 'show']);

        Route::get('/{laQuizGame}/results', [LaQuizGameController::class, 'getQuizGameResult']);
        Route::get('/', [LaQuizGameController::class, 'quizGameHistory']);
    });

    Route::prefix('teachers')->group(function () {
        Route::get('/grade-sections', [LaTeacherController::class, 'teacherGrades']);
        Route::post('/class-students', [LaTeacherController::class, 'getStudents']);
        Route::post('/assign-missions', [LaTeacherController::class, 'assignMissions']);
        Route::post('/assign-topics', [LaTeacherController::class, 'assignTopics']);
        Route::get('/missions', [LaTeacherController::class, 'getTeacherMissions']);
        Route::get('/mission-participants/{laMission}', [LaTeacherController::class, 'getMissionParticipants']);
        Route::patch('/submission/{laMissionComplete}/status', [LaTeacherController::class, 'approveRejectUserMission']);
        Route::get('/visions-list', [LaTeacherController::class, 'index']);
        Route::post('/assign-visions', [LaTeacherController::class, 'assignVision']);
        Route::get('/visions', [LaTeacherController::class, 'getTeacherVisions']);
        Route::get('/vision-participants/{Vision}', [LaTeacherController::class, 'getVisionParticipants']);
        Route::patch('/vision-submission/{visionAnswer}/status', [LaTeacherController::class, 'approveRejectUserVision']);
        Route::get('/visions/{vision}/students/{student}/answers', [LaTeacherController::class, 'getVisionAnswers']);
        Route::get('/vision/{vision}/participants-with-answers', [LaTeacherController::class, 'getVisionParticipantsWithAnswers']);
        Route::get('/leaderboard', [LaTeacherController::class, 'teacherLeaderboard']);
        Route::get('/school-leaderboard', [LaTeacherController::class, 'schoolLeaderboard']);

    });

    Route::get('/leaderboard/teachers', [LaLeaderboardController::class, 'getTeacherLeaderboard']);
    Route::get('/leaderboard/school', [LaLeaderboardController::class, 'schoolLeaderboard']);


    Route::prefix('reports')->group(function () {
        Route::get('/all-students', [LaTrackingReportController::class, 'allStudents']);
        Route::get('/class-students/{laTeacherGrade}', [LaTrackingReportController::class, 'classStudents']);
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
        //Route::post('/{coupon}/redeem', [CouponController::class, 'redeem']);
        Route::match(['get', 'post'], '/{coupon}/redeem', [CouponController::class, 'redeem']);
        Route::get('/teacher/list', [CouponController::class, 'teacherIndex']);
        Route::get('/teacher/purchase-history', [CouponController::class, 'purchaseHistory']);
        Route::get('/teacher/coin-history', [CouponController::class, 'coinHistory']);

    });

    Route::get('/game-reports', [GameReportController::class, 'index']);
    Route::get('/notifications', [UserController::class, 'getNotifications']);

    Route::delete('/notifications', [UserController::class, 'deleteNotifications']);

    Route::post('/clear-notifications', [UserController::class, 'readNotifications']);


    Route::prefix('notifications')->group(function () {
        Route::get('/', [UserController::class, 'getNotifications']);
        Route::delete('/', [UserController::class, 'deleteNotifications']);
        Route::get('/clear', [UserController::class, 'readNotifications']);
    });

    Route::prefix('sessions')->group(function () {
        Route::get('/', [LaSessionController::class, 'mySessions']);
        Route::post('/create', [LaSessionController::class, 'create']);
        Route::get('/upcoming', [LaSessionController::class, 'upcomingSessions']);
        Route::get('/attended', [LaSessionController::class, 'attendSessions']);
        Route::post('/{laSession}/participate', [LaSessionController::class, 'sessionParticipate']);
        Route::get('/{laSession}', [LaSessionController::class, 'getSession']);
        Route::put('/{laSession}', [LaSessionController::class, 'update']);
    });

    Route::get('/campaigns/today', [LaCampaignController::class, 'getTodayCampaigns']);

    Route::get('/TeacherSubjectGrade', [UserController::class, 'TeacherSubjectGrade']);

    Route::get('/faqs', [FaqController::class, 'index']);

    Route::get('/chapters', [ChapterController::class, 'index']);

});
