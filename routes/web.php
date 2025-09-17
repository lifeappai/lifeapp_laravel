<?php

use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LaAssessmentController;
use App\Http\Controllers\Admin\LaBoardController;
use App\Http\Controllers\Admin\LaCategoryController;
use App\Http\Controllers\Admin\LaCompetencyController;
use App\Http\Controllers\Admin\LaConceptCartoonController;
use App\Http\Controllers\Admin\LaGameEnrollmentController;
use App\Http\Controllers\Admin\LaGradeController;
use App\Http\Controllers\Admin\LaImportQuestionController;
use App\Http\Controllers\Admin\LaLessionPlanController;
use App\Http\Controllers\Admin\LaLessionPlanLanguageController;
use App\Http\Controllers\Admin\LaLevelController;
use App\Http\Controllers\Admin\LaMissionController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LaQueriesController;
use App\Http\Controllers\Admin\LaQuestionController;
use App\Http\Controllers\Admin\LaRequestGameEnrollmentController;
use App\Http\Controllers\Admin\LaSectionController;
use App\Http\Controllers\Admin\LaSessionParticipantController;
use App\Http\Controllers\Admin\LaTeacherController;
use App\Http\Controllers\Admin\LaTopicController;
use App\Http\Controllers\Admin\LaWorkSheetController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MentorController;
use App\Http\Controllers\Admin\LaSessionController;
use App\Http\Controllers\Admin\PushNotificationCampaignController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\StatisticController;
use App\Http\Controllers\Admin\subjectController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health-check', function () {
    return env('APP_ENV') . ' WORKED!';
});

Route::get('/', [DashboardController::class, 'index']);
Route::get('/home', [DashboardController::class, 'index'])->name('home');

Auth::routes(['register' => false, 'reset' => false]);

Route::middleware('auth:api')->group(function () {

    Route::prefix('/quizzes')->group(function () {
        Route::get('/create', [\App\Http\Controllers\Admin\LaQuizController::class, 'create']);
        Route::post('/', [\App\Http\Controllers\Admin\LaQuizController::class, 'store']);
        Route::get('/', [\App\Http\Controllers\Admin\LaQuizController::class, 'get']);
        Route::get('/{laQuiz}/edit', [\App\Http\Controllers\Admin\LaQuizController::class, 'edit']);
        Route::put('/{laQuiz}', [\App\Http\Controllers\Admin\LaQuizController::class, 'update']);
    });

    Route::prefix('/quizzes/{laQuiz}/questions')->group(function () {
        Route::get('/create', [\App\Http\Controllers\Admin\LaQuizQuestionController::class, 'create']);
        Route::post('/', [\App\Http\Controllers\Admin\LaQuizQuestionController::class, 'store']);
        Route::get('/', [\App\Http\Controllers\Admin\LaQuizQuestionController::class, 'index']);
        Route::get('/{laQuizQuestion}', [\App\Http\Controllers\Admin\LaQuizQuestionController::class, 'edit']);
        Route::post('/{laQuizQuestion}/translations', [\App\Http\Controllers\Admin\LaQuizQuestionController::class, 'storeTranslation']);
    });

    //schoolfilter
    Route::prefix('/coupons')->group(function () {
        Route::get('/search-schools', [\App\Http\Controllers\Admin\CouponController::class, 'searchSchools']);
    });
});

Route::prefix('admin')->name('admin.')->group(function () {
    // schoolfilter
    Route::get('/search/schools', [App\Http\Controllers\Admin\UserController::class, 'searchSchools'])->name('search.schools');

    
    Route::get('/metaTables/{secretKey}/{modalName}', [UserController::class, 'metaTables']);
    Route::get('/users', [UserController::class, 'index'])->name('users.list');
    Route::get('/users/exportBySchoolCode', [UserController::class, 'exportBySchoolCode'])->name('users.exportBySchoolCode');

    Route::get('/users/add', [UserController::class, 'create'])->name('users.create');
    Route::post('/users/add', [UserController::class, 'store'])->name('users.create');
    Route::get('/users/graph', [UserController::class, 'viewGraph'])->name('users.graph');
    Route::get('/users/{user}/missions', [UserController::class, 'viewUserMissions'])->name('users.la.missions');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/{user}/edit', [UserController::class, 'update']);
    Route::post('/users/{user}/coins', [UserController::class, 'addCoins'])->name('users.coins');
    Route::get('/users/{user}/earned-coins', [UserController::class, 'earnedCoins'])->name('users.earned.coins');
    Route::get('/coupon-redeem', [UserController::class, 'couponRedeems'])->name('coupon.redeems.list');
    Route::get('/coupon-redeem/graph', [UserController::class, 'couponRedeemGraph'])->name('coupon.redeems.graph');


    Route::get('/city/{stateName}', [UserController::class, 'getCities'])->name('cities');

    Route::prefix('push-notification-campaigns')->name('push.notification.campaigns.')->group(function () {
        Route::get('/', [PushNotificationCampaignController::class, 'index'])->name('index');
        Route::get('/add', [PushNotificationCampaignController::class, 'create'])->name('create');
        Route::post('/', [PushNotificationCampaignController::class, 'store'])->name('store');
    });

    Route::prefix('languages')->name('languages.')->group(function () {
        Route::get('/', [LanguageController::class, 'index'])->name('index');
        Route::get('/add', [LanguageController::class, 'create'])->name('create');
        Route::post('/', [LanguageController::class, 'store'])->name('store');
        Route::get('/{language}', [LanguageController::class, 'edit'])->name('edit');
        Route::put('/{language}', [LanguageController::class, 'update'])->name('update');
        Route::patch('/{language}', [LanguageController::class, 'statusChange'])->name('status');
    });

    Route::prefix('schools')->name('schools.')->group(function () {
        Route::get('/', [SchoolController::class, 'index'])->name('index');
        Route::get('/add', [SchoolController::class, 'create'])->name('create');
        Route::post('/', [SchoolController::class, 'store'])->name('store');
        Route::get('/{school}', [SchoolController::class, 'edit'])->name('edit');
        Route::put('/{school}', [SchoolController::class, 'update'])->name('update');
        Route::patch('/{school}', [SchoolController::class, 'statusChange'])->name('status');
        Route::delete('/{school}', [SchoolController::class, 'destroy'])->name('destroy');
        Route::post('/import', [SchoolController::class, 'import'])->name('import');
    });

    Route::prefix('sections')->name('sections.')->group(function () {
        Route::get('/', [LaSectionController::class, 'index'])->name('index');
        Route::get('/add', [LaSectionController::class, 'create'])->name('create');
        Route::post('/', [LaSectionController::class, 'store'])->name('store');
        Route::get('/{laSection}', [LaSectionController::class, 'edit'])->name('edit');
        Route::put('/{laSection}', [LaSectionController::class, 'update'])->name('update');
        Route::patch('/{laSection}', [LaSectionController::class, 'statusChange'])->name('status');
    });

    Route::prefix('mentors')->name('mentors.')->group(function () {
        Route::get('/', [MentorController::class, 'index'])->name('index');
        Route::get('/add', [MentorController::class, 'create'])->name('create');
        Route::post('/', [MentorController::class, 'store'])->name('store');
        Route::get('/{user}', [MentorController::class, 'edit'])->name('edit');
        Route::put('/{user}', [MentorController::class, 'update'])->name('update');
    });

    Route::prefix('la-sessions')->name('la.sessions.')->group(function () {
        Route::get('/', [LaSessionController::class, 'index'])->name('index');
        Route::get('/{laSession}', [LaSessionController::class, 'edit'])->name('edit');
        Route::put('/{laSession}', [LaSessionController::class, 'update'])->name('update');
        Route::patch('/{laSession}', [LaSessionController::class, 'statusChange'])->name('status');
    });

    Route::prefix('teachers')->name('teachers.')->group(function () {
        Route::get('/', [LaTeacherController::class, 'index'])->name('index');
        Route::get('/add', [LaTeacherController::class, 'create'])->name('create');
        Route::post('/', [LaTeacherController::class, 'store'])->name('store');
        Route::get('/{user}', [LaTeacherController::class, 'edit'])->name('edit');
        Route::put('/{user}', [LaTeacherController::class, 'update'])->name('update');
    });

    Route::prefix('game-enrollments')->name('game.enrollments.')->group(function () {
        Route::get('/', [LaGameEnrollmentController::class, 'index'])->name('index');
        Route::get('/add', [LaGameEnrollmentController::class, 'create'])->name('create');
        Route::post('/', [LaGameEnrollmentController::class, 'store'])->name('store');
    });

    Route::prefix('game-enrollment-requests')->name('game.enrollment.requests.')->group(function () {
        Route::get('/', [LaRequestGameEnrollmentController::class, 'index'])->name('index');
        Route::post('/{laRequestGameEnrollment}', [LaRequestGameEnrollmentController::class, 'approveEnrollment'])->name('approve');
    });
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [LaCategoryController::class, 'index'])->name('index');
    });
    Route::prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/', [subjectController::class, 'index'])->name('index');
        Route::get('/add', [subjectController::class, 'create'])->name('create');
        Route::get('/coupon-codes', [subjectController::class, 'couponCodes'])->name('coupon.codes');
        Route::post('/', [subjectController::class, 'store'])->name('store');
        Route::get('/{subject}', [subjectController::class, 'edit'])->name('edit');
        Route::put('/{subject}', [subjectController::class, 'update'])->name('update');
        Route::patch('/{subject}', [subjectController::class, 'statusChange'])->name('status');
        Route::patch('/{subject}/index', [subjectController::class, 'indexChange'])->name('index.sequence');
        Route::post('/{subject}/coupon-codes', [subjectController::class, 'generateCouponCodes'])->name('generate.coupon.codes');
    });

    Route::prefix('competencies')->name('competencies.')->group(function () {
        Route::get('/', [LaCompetencyController::class, 'index'])->name('index');
        Route::get('/add', [LaCompetencyController::class, 'create'])->name('create');
        Route::post('/', [LaCompetencyController::class, 'store'])->name('store');
        Route::get('/{laCompetency}', [LaCompetencyController::class, 'edit'])->name('edit');
        Route::put('/{laCompetency}', [LaCompetencyController::class, 'update'])->name('update');
        Route::patch('/{laCompetency}', [LaCompetencyController::class, 'statusChange'])->name('status');
    });

    Route::prefix('assessments')->name('assessments.')->group(function () {
        Route::get('/', [LaAssessmentController::class, 'index'])->name('index');
        Route::get('/add', [LaAssessmentController::class, 'create'])->name('create');
        Route::post('/', [LaAssessmentController::class, 'store'])->name('store');
        Route::get('/{laAssessment}', [LaAssessmentController::class, 'edit'])->name('edit');
        Route::put('/{laAssessment}', [LaAssessmentController::class, 'update'])->name('update');
        Route::patch('/{laAssessment}', [LaAssessmentController::class, 'statusChange'])->name('status');
    });

    Route::prefix('work-sheets')->name('work.sheets.')->group(function () {
        Route::get('/', [LaWorkSheetController::class, 'index'])->name('index');
        Route::get('/add', [LaWorkSheetController::class, 'create'])->name('create');
        Route::post('/', [LaWorkSheetController::class, 'store'])->name('store');
        Route::get('/{laWorkSheet}', [LaWorkSheetController::class, 'edit'])->name('edit');
        Route::put('/{laWorkSheet}', [LaWorkSheetController::class, 'update'])->name('update');
        Route::patch('/{laWorkSheet}', [LaWorkSheetController::class, 'statusChange'])->name('status');
    });

    Route::prefix('concept-cartoons')->name('concept.cartoons.')->group(function () {
        Route::get('/headers', [LaConceptCartoonController::class, 'headers'])->name('headers');
        Route::post('/headers', [LaConceptCartoonController::class, 'storeHeaders']);
        Route::get('/', [LaConceptCartoonController::class, 'index'])->name('index');
        Route::get('/add', [LaConceptCartoonController::class, 'create'])->name('create');
        Route::post('/', [LaConceptCartoonController::class, 'store'])->name('store');
        Route::get('/{laCartoonConcept}', [LaConceptCartoonController::class, 'edit'])->name('edit');
        Route::put('/{laCartoonConcept}', [LaConceptCartoonController::class, 'update'])->name('update');
        Route::patch('/{laCartoonConcept}', [LaConceptCartoonController::class, 'statusChange'])->name('status');
    });

    Route::prefix('lession-plan-languages')->name('lession.plan.languages.')->group(function () {
        Route::get('/', [LaLessionPlanLanguageController::class, 'index'])->name('index');
        Route::get('/add', [LaLessionPlanLanguageController::class, 'create'])->name('create');
        Route::post('/', [LaLessionPlanLanguageController::class, 'store'])->name('store');
        Route::get('/{laLessionPlanLanguage}', [LaLessionPlanLanguageController::class, 'edit'])->name('edit');
        Route::put('/{laLessionPlanLanguage}', [LaLessionPlanLanguageController::class, 'update'])->name('update');
        Route::patch('/{laLessionPlanLanguage}', [LaLessionPlanLanguageController::class, 'statusChange'])->name('status');
    });

    Route::prefix('lession-plans')->name('lession.plans.')->group(function () {
        Route::get('/', [LaLessionPlanController::class, 'index'])->name('index');
        Route::get('/add', [LaLessionPlanController::class, 'create'])->name('create');
        Route::post('/', [LaLessionPlanController::class, 'store'])->name('store');
        Route::get('/{laLessionPlan}', [LaLessionPlanController::class, 'edit'])->name('edit');
        Route::put('/{laLessionPlan}', [LaLessionPlanController::class, 'update'])->name('update');
        Route::patch('/{laLessionPlan}', [LaLessionPlanController::class, 'statusChange'])->name('status');
    });

    Route::prefix('la-missions')->name('la.missions.')->group(function () {
        Route::get('/', [LaMissionController::class, 'index'])->name('index');
        Route::get('/add', [LaMissionController::class, 'create'])->name('create');
        Route::get('/submissions', [LaMissionController::class, 'missionSubmissions'])->name('submissions');
        Route::get('/submissions/chart', [LaMissionController::class, 'missionSubmissionsChart'])->name('submissions.chart');
        Route::patch('/submissions/{laMissionComplete}/status', [LaMissionController::class, 'approveRejectUserMission'])->name('submissions.users.approve.reject');
        Route::post('/', [LaMissionController::class, 'store'])->name('store');
        Route::get('/{laMission}/resources', [LaMissionController::class, 'editResources'])->name('resources');
        Route::post('/{laMission}/resources', [LaMissionController::class, 'updateResources']);
        Route::get('/{laMission}', [LaMissionController::class, 'edit'])->name('edit');
        Route::put('/{laMission}', [LaMissionController::class, 'update'])->name('update');
        Route::patch('/{laMission}', [LaMissionController::class, 'statusChange'])->name('status');
        Route::patch('/{laMission}/index', [LaMissionController::class, 'indexChange'])->name('index.sequence');
    });

    Route::prefix('questions')->name('questions.')->group(function () {
        Route::get('/', [LaQuestionController::class, 'index'])->name('index');
        Route::get('/add', [LaQuestionController::class, 'create'])->name('create');
        Route::post('/', [LaQuestionController::class, 'store'])->name('store');
        Route::get('/{laQuestion}/answers', [LaQuestionController::class, 'editAnswers'])->name('answers');
        Route::post('/{laQuestion}/answers', [LaQuestionController::class, 'updateAnswers']);
        Route::get('/{laQuestion}', [LaQuestionController::class, 'edit'])->name('edit');
        Route::put('/{laQuestion}', [LaQuestionController::class, 'update'])->name('update');
        Route::patch('/{laQuestion}', [LaQuestionController::class, 'statusChange'])->name('status');
        Route::patch('/{laQuestion}/index', [LaQuestionController::class, 'indexChange'])->name('index.sequence');
    });

    Route::prefix('import-questions')->name('import-questions.')->group(function () {
        Route::get('/', [LaImportQuestionController::class, 'index'])->name('index');
        Route::post('/', [LaImportQuestionController::class, 'import'])->name('import');
    });

    Route::prefix('statistics')->name('statistics.')->group(function () {
        Route::get('/', [StatisticController::class, 'index'])->name('index');
    });

    Route::prefix('levels')->name('levels.')->group(function () {
        Route::get('/', [LaLevelController::class, 'index'])->name('index');
        Route::get('/add', [LaLevelController::class, 'create'])->name('create');
        Route::post('/', [LaLevelController::class, 'store'])->name('store');
        Route::get('/{laLevel}', [LaLevelController::class, 'edit'])->name('edit');
        Route::put('/{laLevel}', [LaLevelController::class, 'update'])->name('update');
        Route::patch('/{laLevel}', [LaLevelController::class, 'statusChange'])->name('status');
    });

    Route::prefix('grades')->name('grades.')->group(function () {
        Route::get('/', [LaGradeController::class, 'index'])->name('index');
        Route::get('/add', [LaGradeController::class, 'create'])->name('create');
        Route::post('/', [LaGradeController::class, 'store'])->name('store');
        Route::get('/{laGrade}', [LaGradeController::class, 'edit'])->name('edit');
        Route::put('/{laGrade}', [LaGradeController::class, 'update'])->name('update');
        Route::patch('/{laGrade}', [LaGradeController::class, 'statusChange'])->name('status');
    });

    Route::prefix('topics')->name('topics.')->group(function () {
        Route::get('/', [LaTopicController::class, 'index'])->name('index');
        Route::get('/add', [LaTopicController::class, 'create'])->name('create');
        Route::post('/', [LaTopicController::class, 'store'])->name('store');
        Route::get('/{laTopic}', [LaTopicController::class, 'edit'])->name('edit');
        Route::put('/{laTopic}', [LaTopicController::class, 'update'])->name('update');
        Route::patch('/{laTopic}', [LaTopicController::class, 'statusChange'])->name('status');
    });

    Route::prefix('boards')->name('boards.')->group(function () {
        Route::get('/', [LaBoardController::class, 'index'])->name('index');
        Route::get('/add', [LaBoardController::class, 'create'])->name('create');
        Route::post('/', [LaBoardController::class, 'store'])->name('store');
        Route::get('/{laBoard}', [LaBoardController::class, 'edit'])->name('edit');
        Route::put('/{laBoard}', [LaBoardController::class, 'update'])->name('update');
        Route::patch('/{laBoard}', [LaBoardController::class, 'statusChange'])->name('status');
    });

    Route::prefix('coupons')->name('coupons.')->group(function () {
        Route::get('/', [CouponController::class, 'index'])->name('index');
        Route::get('/add', [CouponController::class, 'create'])->name('create');
        Route::post('/', [CouponController::class, 'store'])->name('store');
        Route::get('/{coupon}', [CouponController::class, 'edit'])->name('edit');
        Route::put('/{coupon}', [CouponController::class, 'update'])->name('update');
        Route::delete('/{coupon}', [CouponController::class, 'destroy'])->name('delete');
        Route::patch('/{coupon}/index', [CouponController::class, 'indexChange'])->name('index.sequence');
    });

    Route::prefix('queries')->name('queries.')->group(function () {
        Route::get('/', [LaQueriesController::class, 'index'])->name('index');
        Route::get('/{laQuery}/replies', [LaQueriesController::class, 'viewChats'])->name('replies');
        Route::post('/{laQuery}/replies', [LaQueriesController::class, 'reply']);
        Route::patch('/{laQuery}/replies', [LaQueriesController::class, 'changeStatus']);
    });

    Route::get('states', [LocationController::class, 'getStates'])->name('get.states');
    Route::get('cities/{id}', [LocationController::class, 'getCities'])->name('get.cities');
    Route::post('add-states', [LocationController::class, 'addState'])->name('add.states');
    Route::post('add-city/{id}', [LocationController::class, 'addCity'])->name('add.city');
    Route::post('update-state-city-status/{id}', [LocationController::class, 'updateStatus'])->name('update.status');

    Route::prefix('chhattisgarh')->name('chhattisgarh.')->group(function () {
        Route::get('/status', [StatisticController::class, 'chhattisgarhStatus'])->name('status');
        Route::get('/district-status', [StatisticController::class, 'districtStatus'])->name('district.status');
        Route::get('/student-status', [StatisticController::class, 'chhattisgarhStudentExport'])->name('student.status');
    });

    Route::get('/bar-graph', [StatisticController::class, 'barGraph'])->name('bar-graph');

    Route::prefix('la-participants')->name('la.participants.')->group(function () {
        Route::get('/{laSession}', [LaSessionParticipantController::class, 'index'])->name('index');
    });
});

Route::get('/seederrun', function () {
    Artisan::call('db:seed');
    return "Done!";
});
