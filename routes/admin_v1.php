<?php


use App\Http\Controllers\Api\Web\MoviesController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\V1\UserController;
use \App\Http\Controllers\Api\V1\SchoolController;
use App\Http\Controllers\Api\Web\CategoryController;
use App\Http\Controllers\Api\Web\CouponController;
use App\Http\Controllers\Api\Web\MissionsController;

/*
 | ---------------------------------------------------------
 | Authentication API Routes
 | ---------------------------------------------------------
 */
Route::post('/login', [\App\Http\Controllers\Api\Web\UserController::class,'login']);

Route::patch('mission-completes/{missionComplete}', [MissionsController::class, 'approveSubmission']);

Route::middleware('auth:api')->group(function () {
    /*
     | ---------------------------------------------------------
     |  Mission Routes
     | ---------------------------------------------------------
     */
    Route::get('users', [\App\Http\Controllers\Api\Web\UserController::class, 'index']);

    Route::get('users/{user}/mission-completes', [\App\Http\Controllers\Api\Web\UserController::class, 'missionCompleted']);

    Route::post('missions', [MissionsController::class, 'create']);
    Route::get('missions', [MissionsController::class, 'index']);
    Route::get('missions/{mission}', [MissionsController::class, 'show']);
    Route::put('missions/{mission}', [MissionsController::class, 'update']);
    Route::delete('missions/{mission}', [MissionsController::class, 'delete']);
    Route::post('missions/{mission}/questions', [MissionsController::class, 'createQuestion']);
    Route::post('missions/{mission}/images', [MissionsController::class, 'addImages']);
    Route::delete('mission-images/{image}', [MissionsController::class, 'deleteImages']);

    Route::patch('user-submissions/{missionComplete}', [MissionsController::class, 'approveSubmission']);

    Route::prefix('missions')->group(function () {

        Route::get('/list', [MissionsController::class, 'getMissions']);
        Route::post('/get-mission', [MissionsController::class, 'getMission']);
        Route::post('/add-mission-image', [MissionsController::class, 'addMissionImage']);
        Route::post('/create', [MissionsController::class, 'createMission']);
        Route::post('/update-mission', [MissionsController::class, 'updateMission']);
        Route::post('/add-question', [MissionsController::class, 'addMissionQuestion']);
        Route::get('/delete/{id}', [MissionsController::class, 'deleteMission']);
        Route::get('/mission-image/delete/{id}', [MissionsController::class, 'deleteMissionImage']);
        Route::post('/teacher-rating', [MissionsController::class, 'teacherRating']);

        Route::patch('{mission}/question-documents/{document}', [MissionsController::class, 'deleteDocuments']);
        Route::get('{mission}/user-submissions', [MissionsController::class, 'userSubmissions']);
    });
    /*
     | ---------------------------------------------------------
     |  Category Routes
     | ---------------------------------------------------------
     */

    Route::prefix('category')->group(function () {
        Route::get('/list', [CategoryController::class, 'index']);
        Route::post('/create-category', [CategoryController::class, 'createCategory']);
    });

     /*
     | ---------------------------------------------------------
     |  Coupons Routes
     | ---------------------------------------------------------
     */

    Route::prefix('coupon')->group(function () {
        Route::get('/list', [CouponController::class, 'index']);
        Route::post('/create-coupon', [CouponController::class, 'createCoupon']);
    });

     /*
     | ---------------------------------------------------------
     |  Movies Routes
     | ---------------------------------------------------------
     */

    Route::prefix('movies')->group(function () {

        Route::post('/create-subject', [MoviesController::class, 'createSubject']);
        Route::post('/create-subjects', [MoviesController::class, 'createSubjects']);
        Route::put('/update-subject', [MoviesController::class, 'updateSubject']);
        Route::put('/update-subjects', [MoviesController::class, 'updateSubjects']);
        Route::post('/create-levels', [MoviesController::class, 'createLevels']);
        Route::put('/update-levels', [MoviesController::class, 'updateLevels']);
        Route::post('/create-level', [MoviesController::class, 'createLevel']);
        Route::put('/update-level', [MoviesController::class, 'updateLevel']);
        Route::post('/create-topic', [MoviesController::class, 'createTopic']);
        Route::put('/update-topic', [MoviesController::class, 'updateTopic']);
        Route::post('/create-topics', [MoviesController::class, 'createTopics']);
        Route::put('/update-topics', [MoviesController::class, 'updateTopics']);
        Route::post('/create-movie', [MoviesController::class, 'createMovie']);
        Route::put('/update-movie', [MoviesController::class, 'updateMovie']);
        Route::post('/create-quiz', [MoviesController::class, 'createQuiz']);
        Route::post('/create-question', [MoviesController::class, 'createQuestion']);
        Route::post('/add-question', [MoviesController::class, 'addQuestion']);
        Route::post('/create-question-option', [MoviesController::class, 'createQuestionOption']);
        Route::get('/subjects', [MoviesController::class, 'index']);
        Route::post('/get-subject', [MoviesController::class, 'getSubject']);
        Route::post('/topics', [MoviesController::class, 'getTopics']);
        Route::post('/get-topic', [MoviesController::class, 'getTopicById']);
        Route::post('/movie', [MoviesController::class, 'getMovie']);
        Route::post('/quiz', [MoviesController::class, 'getQuiz']);
        Route::put('/update-quiz', [MoviesController::class, 'updateQuiz']);
        Route::post('/update-question', [MoviesController::class, 'updateQuestion']);
        Route::get('/subject/delete/{id}', [MoviesController::class, 'deleteSubject']);
        Route::get('/level/delete/{id}', [MoviesController::class, 'deleteLevel']);
        Route::get('/topic/delete/{id}', [MoviesController::class, 'deleteTopic']);
        Route::get('/movie/delete/{id}', [MoviesController::class, 'deleteMovie']);
        Route::get('/quiz/delete/{id}', [MoviesController::class, 'deleteQuiz']);
        Route::get('/question/delete/{id}', [MoviesController::class, 'deleteQuestion']);
    });
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
