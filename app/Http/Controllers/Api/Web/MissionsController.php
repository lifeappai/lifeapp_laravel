<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\MissionComplete;
use App\Models\Mission;
use App\Models\Media;
use App\Models\MissionQuestionTranslation;
use App\Models\MissionImage;
use App\Models\UserMissionComplete;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\MissionResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\MediaUpload;
use App\Http\Traits\CoinTrait;

class MissionsController extends Controller
{
    use MediaUpload, CoinTrait;

    /**
     * @return JsonResponse
     */
    public function index()
    {
        $mission = Mission::get();

        return new JsonResponse([
            'missions' => \App\Http\Resources\Admin\MissionResource::collection($mission)
        ],Response::HTTP_OK);
    }

    /**
     * @param Mission $mission
     * @return JsonResponse
     */
    public function show(Mission $mission)
    {
        return new JsonResponse(['mission' => new \App\Http\Resources\Admin\MissionResource($mission)],Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'brain_points' => ['required', 'string'],
            'heart_points' => ['required', 'string'],
            'mission_type' => ['required', 'string'],
            'flag' => ['nullable', 'integer'],

            'translations' => ['required', 'array'],
            'translations.*.name' => ['required', 'string'],
            'translations.*.question.title' => ['required', 'string'],
            'translations.*.question.document' => ['required', 'mimes:jpeg,png,jpg,pdf'],
            'translations.*.images.*' => ['required', 'mimes:jpeg,png,jpg'],
        ]);

        $mission = Mission::create([
            'mission_name' => json_encode([]),
            'mission_type' => $data['mission_type'],
            'flag' => 0,
            'brain_points' => $data['brain_points'],
            'heart_points' => $data['heart_points'],
        ]);

        $translations = $data['translations'];
        $names = $mission->mission_name;

        foreach ($translations as $locale => $data) {
            $names[$locale] = $data['name'];

            $question = $data['question'];
            $mission->createQuestionDocument($locale, $question);

            foreach($data['images'] as $image) {
                $mission->createImage($locale, $image);
            }
        }
        $mission->mission_name = $names;
        $mission->save();

        $mission->sendNotification();

        return new JsonResponse([
            'mission' => new \App\Http\Resources\Admin\MissionResource($mission)
        ]);
    }

    /**
     * @param Request $request
     * @param Mission $mission
     * @return JsonResponse
     */
    public function createQuestion(Request $request, Mission $mission)
    {
        $data = $request->validate([
            'locale' => ['required', 'string'],
            'title' => ['string'],
            'document' => ['mimes:jpeg,png,jpg,pdf'],
        ]);

        $questionData = [];
        if (isset($data['title'])) {
            $questionData['question_title'] = $data['title'];
        }
        if (isset($data['document'])) {
            $image = $data['document'];
            $mediaName = $image->getClientOriginalName();
            $mediaPath = Storage::put('media', $image);
            $media = Media::create([
                'name' => $mediaName,
                'path' => $mediaPath
            ]);
            $questionData['question_media_id'] = $media->id;
        }
        $question = $mission->missionQuestions()->updateOrCreate([
            'locale' => $data['locale'],
        ], $questionData);

        return new JsonResponse([
            'mission' => new \App\Http\Resources\Admin\MissionQuestionResource($question)
        ]);
    }

    /**
     * @param Request $request
     * @param Mission $mission
     * @return JsonResponse
     */
    public function addImages(Request $request, Mission $mission)
    {
        $data = $request->validate([
            'locale' => ['required', 'string'],
            'image' => ['required', 'mimes:jpeg,png,jpg'],
        ]);

        $image = $data['image'];
        $image = $mission->createImage($data['locale'], $image);

        return new JsonResponse([
            'mission' => new \App\Http\Resources\Admin\MissionImageResource($image)
        ]);
    }

    /**
     * @param MissionImage $image
     * @return JsonResponse
     */
    public function deleteImages(MissionImage $image)
    {
        $image->delete();
        return new JsonResponse([
            'message' => "Image deleted successfully",
        ]);
    }

    public function delete(Mission $mission)
    {
        $mission->delete();
        return new JsonResponse(['message' => "Mission deleted"],Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param Mission $mission
     */
    public function update(Request $request, Mission $mission)
    {
        $data = $request->validate([
            'brain_points' => ['string'],
            'heart_points' => ['string'],
            'mission_type' => ['string'],
            'flag' => ['nullable', 'integer'],

            'translations' => ['array'],
            'translations.*.name' => ['string'],
            'translations.*.question.title' => ['string'],
            'translations.*.question.document' => ['integer'],
            'translations.*.images.*' => ['integer'],
        ]);



        $mission->update(Arr::only($data, ['brain_points', 'heart_points', 'mission_type']));

        if (isset($data['translations'])) {
            $translations = $data['translations'];
            $names = $mission->mission_name;

            foreach ($translations as $locale => $info) {
                if (isset($info['name'])) {
                    $names[$locale] = $info['name'];
                }

                if (isset($info['question'])) {
                    $question = $info['question'];
                    $mission->updateQuestion($locale, $question);
                }

                if (isset($info['images']) && count($info['images']) > 0) {
                    $mission->missionImages()->delete();
                    foreach ($info['images'] as $imgId) {
                        $mission->missionImages()->create([
                            'locale' => $locale,
                            'mission_media_id' => $imgId,
                        ]);
                    }
                }
            }

            $mission->mission_name = $names;
            $mission->save();
        }

        return new JsonResponse([
            'mission' => new \App\Http\Resources\Admin\MissionResource($mission)
        ]);
    }

    /**
     * @param Mission $mission
     * @return JsonResponse
     */
    public function userSubmissions(Mission $mission)
    {
        $submissions = $mission->missionCompletes()->with('user')->get();

        $data = [];
        foreach ($submissions as $submission) {

            $missionMedia = $mission->missionUploads()->with('media')
                ->ofUser($submission->user_id)->first();

            $data[] = [
                'id' => $submission->id,
                'mission_id' => $submission->mission_id,
                'description' => $submission->description,
                'status' => $submission->status,
                'user' => $submission->user,
                'document' => $missionMedia ? $missionMedia : null,
                'created_at' => $submission->created_at,
                'updated_at' => $submission->updated_at,
            ];
        }

        return new JsonResponse(['submissions' => $data], \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param MissionComplete $missionComplete
     * @return JsonResponse
     */
    public function approveSubmission(Request $request, MissionComplete $missionComplete)
    {
        $data = $request->validate([
            'status' => 'required|in:-1,1',
            'rating' => 'in:1,2,3,4,5',
            'comment' => 'string|max:255',
        ]);

        $comment = isset($data['comment']) ? $data['comment'] : null;
        $mission = $missionComplete->mission;

        if (isset($data['rating'])) {
            $missionComplete->rating = $data['rating'];
            $points = $mission->getPoints();
            $earnPoint = $this->coinCalculation($points, $data['rating']);

            UserMissionComplete::updateOrCreate([
                'user_id' => $missionComplete->user_id,
                'mission_id' => $missionComplete->mission_id,
            ], [
                'rating' => $data['rating'],
                'mission_type' => $mission->mission_type,
                'earn_points' => $earnPoint,
            ]);
        }
        $user = $missionComplete->user;

        if ($data['status'] == 1) {
            $missionComplete->approved($comment);
            $mission->sendApproveNotification($user);
        } else {
            $missionComplete->rejected($comment);
            $mission->sendRejectNotification($user);
        }

        return new JsonResponse([
            'submissions' => "Submission updated successfully"
        ], 200);
    }

    public function deleteDocuments(Mission $mission, $document)
    {
        MissionQuestionTranslation::where(['mission_id' => $mission->id])
            ->where('question_media_id', $document)->delete();

        return new JsonResponse([
            'message' => 'Document has been deleted successfully.',
        ], Response::HTTP_OK);
    }


    /*********************** DEPRICATED ************************/


    /**
     * @return JsonResponse
     */
    public function getMissions() : JsonResponse
    {
        return new JsonResponse(['missions' => MissionResource::collection(Mission::get())],Response::HTTP_OK);
    }

    public function getMission(Request $request): JsonResponse
    {
        $data = $request->validate(
            [
                "mission_id"             => ['required', 'integer'],
            ]
        );
        $mission = Mission::where('id', '=', $data['mission_id'])->get();

        return new JsonResponse(['missions' => MissionResource::collection($mission)],Response::HTTP_OK);
    }

    public function createMission(Request $request) : JsonResponse
    {
        $data = $request->validate(
            [
                "mission_name" => ['required', 'string'],
                'brain_points' => ['required', 'string'],
                'heart_points' => ['required', 'string'],
                'mission_type' => ['required', 'string'],
                'locale' => ['required', 'string'],
                'flag' => ['nullable', 'integer'],
                'question_title' => ['required'],
                'question_document' => ['required', 'mimes:jpeg,png,jpg,pdf'],
                'image.*' => ['required', 'mimes:jpeg,png,jpg'],
            ]
        );
        $mission = Mission::create(
            [
                'mission_name' => $data['mission_name'],
                'mission_type' => $data['mission_type'],
                'flag' => 0,
                'locale' => $data['locale'],
                'brain_points' => $data['brain_points'],
                'heart_points' => $data['heart_points'],
            ]
        );
        $missionId = $mission->id;
        /* Add Mission Question */
        $image = $data['question_document'];

        $mediaName = $image->getClientOriginalName();
        $mediaPath = Storage::put('media', $image);
        $media = Media::create(
            [
                'name' => $mediaName,
                'path' => $mediaPath
            ]
        );
        MissionQuestionTranslation::create(
            [
                'mission_id' => $missionId,
                'locale' => $data['locale'],
                'question_title' => $data['question_title'],
                'question_media_id' => $media->id,
            ]
        );
        /* Add Mission Images */
        foreach ($data['image'] as $img) {

            $image = $img;

            $mediaName = $image->getClientOriginalName();
            $mediaPath = Storage::put('media', $image);
            $media = Media::create(
                [
                    'name' => $mediaName,
                    'path' => $mediaPath
                ]
            );

            MissionImage::create(
                [
                    'mission_id' => $missionId,
                    'locale' => $data['locale'],
                    'mission_media_id' => $media->id,
                ]
            );
        }

        $mission->sendNotification();

        $response = [
            'data' => $missionId,
            'message' => "Mission created successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addMissionQuestion(Request $request) : JsonResponse{

        $data = $request->validate([
            "mission_id"         => ['required', 'integer'],
            'locale'             => ['required', 'string'],
            'question_title'     => ['required', 'string'],
            'question_document'  => ['required', 'mimes:jpeg,png,jpg,pdf'],
        ]);
        $image = $data['question_document'];

        $mediaName = $image->getClientOriginalName();
        $mediaPath = Storage::put('media', $image );
        $media = Media::create(
            [
                'name'    => $mediaName,
                'path'    => $mediaPath
            ]
        );

        MissionQuestionTranslation::create(
            [
                'mission_id' => $data['mission_id'],
                'locale' => $data['locale'],
                'question_title' => $data['question_title'],
                'question_media_id'  => $media->id,
            ]
        );
        $response = [
            'message' => "Question created successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addMissionImage(Request $request) : JsonResponse{

        $data = $request->validate(
            [
                "mission_id"         => ['required', 'integer'],
                'locale'             => ['required', 'string'],
                'image'              => ['required', 'mimes:jpeg,png,jpg'],
            ]
        );
        $image = $data['image'];

        $mediaName = $image->getClientOriginalName();
        $mediaPath = Storage::put('media', $image );
        $media = Media::create(
            [
                'name'    => $mediaName,
                'path'    => $mediaPath
            ]
        );

        MissionImage::create(
            [
                'mission_id' => $data['mission_id'],
                'locale' => $data['locale'],
                'mission_media_id'  => $media->id,
            ]
        );
        $response = [
            'message' => "Image created successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function deleteMission($id): JsonResponse
    {
        $mission = Mission::FindOrFail($id);
        $mission->missionQuestions()->delete();
        $mission->missionImages()->delete();
        $mission->delete();
        return new JsonResponse(['message' => "Mission deleted"],Response::HTTP_OK);
    }

    public function deleteMissionImage($id): JsonResponse
    {
        $missionImg = MissionImage::FindOrFail($id);
        $missionImg->delete();
        return new JsonResponse(['message' => "Mission image deleted"],Response::HTTP_OK);
    }

    public function updateMission(Request $request) : JsonResponse
    {
        $data = $request->validate(
            [
                "mission_id"              => ['required', 'integer'],
                "mission_name"            => ['required', 'string'],
                'locale'                  => ['required', 'string'],
                'brain_points'            => ['required', 'string'],
                'heart_points'            => ['required', 'string'],
                'mission_type'            => ['required', 'string'],
                'question_title'          => ['required'],
                'question_document'       => ['mimes:jpeg,png,jpg,pdf'],
                'image.*'                 => ['mimes:jpeg,png,jpg'],
            ]
        );

        $mission = Mission::where(['id' => $data["mission_id"]])->update([
            'mission_name' => $data['mission_name'],
            'mission_type' => $data['mission_type'],
            'locale'  => $data['locale'],
            'brain_points' => $data['brain_points'],
            'heart_points' => $data['heart_points'],
        ]);

        /* Update mission document*/
        MissionQuestionTranslation::where(['mission_id' => $data["mission_id"]])->update([
            'question_title' => $data['question_title'],
            'locale'  => $data['locale'],
        ]);
        if($request->hasFile('question_document')){
            $media = $this->upload($data['question_document']);
            MissionQuestionTranslation::where(['mission_id' => $data["mission_id"]])->update([
                'question_media_id'  => $media->id,
            ]);
        }

        /* Add Mission Images */
        if(!empty($data['image'])){
            foreach($data['image'] as $img){

                $image = $img;

                $media = $this->upload($image);

                MissionImage::create(
                    [
                        'mission_id' => $data["mission_id"],
                        'locale'  => $data['locale'],
                        'mission_media_id'  => $media->id,
                    ]
                );
            }
        }

        $response = [
            'message' => "Mission updated successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function teacherRating(Request $request)
    {
        $data = $request->validate(
            [
                "mission_id" => ['required', 'integer'],
                "user_id" => ['required', 'string'],
                'rating' => ['required', 'string'],
            ]
        );
        $mission = Mission::find($data['mission_id']);
        $mission_type = $mission->mission_type;

        if ($mission->type === "brain") {
            $point = $mission->brain_points;
        } else {
            $point = $mission->heart_points;
        }
        $earnPoint = $this->coinCalculation($point, $data['rating']);
        $user_mission_complete = UserMissionComplete::updateOrCreate(
            [
                'user_id' => $data['user_id'],
                'mission_id' => $data['mission_id'],
            ],
            [
                'rating' => $data['rating'],
                'mission_type' => $mission_type,
                'earn_points' => $earnPoint,
            ]);
        return new JsonResponse(['success' => true], Response::HTTP_OK);
    }
}
