<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\V1\DeviceRequest;
use App\Http\Requests\Auth\V1\MediaRequest;
use App\Http\Requests\Auth\V1\RegisterRequest;
use App\Http\Requests\Auth\V1\SignUpRequest;
use App\Http\Requests\Auth\V1\UpdatePinRequest;
use App\Http\Resources\API\V1\MediaResource;
use App\Http\Resources\API\V1\UserResource;
use App\Models\School;
use App\Models\Students;
use App\Models\User;
use ErrorException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthenticationController extends Controller
{
	/**
	 * @param RegisterRequest $request
	 *
	 * @return JsonResponse
	 * @throws Throwable
	 */
	public function signUp(RegisterRequest $request): JsonResponse
	{
		$data = $request->validated();

		$users = User::where(['mobile_no' => $data['mobile_no'], 'school_id' => $data['school_id']])->count();

		throw_if($users >= 3, new ErrorException('Number already exist', Response::HTTP_UNPROCESSABLE_ENTITY));

		$studentRegistered = Students::where(
			[
				'id'          => $data['student_id'] ?? null,
				'mobile_no'   => $data['mobile_no'],
				'is_register' => 2
			]
		)->count();
		throw_if(
			$data['school_id'] != '-1' && $studentRegistered >= 1,
			new ErrorException('Student already register', Response::HTTP_UNPROCESSABLE_ENTITY)
		);

		$user = User::create($data);
		$data['type'] = UserType::Student;
		$data['user_id'] = $user->id;

		if (isset($data['student_id'])) {
			$studentID = $data['student_id'];
			$data['is_register'] = 2;
			Students::find($studentID)->update($data);
		} else {
			$data['is_register'] = 1;
			$student = Students::create($data);
			$studentID = $student->id;
		}

		$user->student_id = $studentID;
		$user->accessToken = $user->createToken('LifeApp')->accessToken;

		Auth::setUser($user);

		return new JsonResponse(
			[
				'message' => 'Student Register Successfully',
				'user' => new UserResource($user)
			], Response::HTTP_OK
		);
	}

	public function newRegister(RegisterRequest $request): JsonResponse
	{
		$data = $request->validated();
		$user = User::where(['mobile_no' => $data['mobile_no']])->first();
		if ($user) {
			$user = User::find($user->id);
			$students = Students::where(['user_id' => $user->id])->count();
			if ($students >= 3) {
				return new JsonResponse(['message' => 'Number Already registered'], Response::HTTP_UNAUTHORIZED);
			} else {
				$studentCount =
					Students::where(['mobile_no' => $data['mobile_no'], 'school_id' => $data['school_id']])->first();
				if ($studentCount) {
					$studentlist =
						Students::where(['mobile_no' => $data['mobile_no'], 'school_id' => $data['school_id']])->get();
					foreach ($studentlist as $student) {
						$updateData = [
							'user_id'     => $user->id,
							'is_register' => 2,
						];
						Students::where(['mobile_no' => $data['mobile_no'], 'school_id' => $data['school_id']])->update(
							$updateData
						);
						$user->student_id = $student->id;
						$user->school_id = $data['school_id'];
					}
				} else {
					$student = [
						'school_id'   => $data['school_id'],
						'type'        => UserType::Student,
						'name'        => $data['name'],
						'dob'         => $data['dob'],
						'gender'      => $data['gender'],
						'grade'       => $data['grade'],
						'address'     => $data['address'],
						'city'        => $data['city'],
						'state'       => $data['state'],
						'mobile_no'   => $data['mobile_no'],
						'user_id'     => $user->id,
						'is_register' => 1,
					];
					$studentID = Students::insertGetId($student);
					$user->student_id = $studentID;
					$user->school_id = $data['school_id'];
				}
				// $user = User::find($user->id);
				// $user->accessToken = $user->createToken('LifeApp')->accessToken;
				// $user->student_id = $studentID;
				return new JsonResponse(
					['message' => 'Student Register Successfully', 'user' => new UserResource($user)], Response::HTTP_OK
				);
			}
			return new JsonResponse(['message' => $students], Response::HTTP_UNAUTHORIZED);

		} else {
			$userID = User::insertGetId($data);
			$studentCount =
				Students::where(['mobile_no' => $data['mobile_no'], 'school_id' => $data['school_id']])->first();
			$user = User::find($userID);
			if ($studentCount) {
				$studentlist =
					Students::where(['mobile_no' => $data['mobile_no'], 'school_id' => $data['school_id']])->get();
				foreach ($studentlist as $student) {
					$updateData = [
						'user_id'     => $userID,
						'is_register' => 2,
					];
					Students::where(['mobile_no' => $data['mobile_no'], 'school_id' => $data['school_id']])->update(
						$updateData
					);
					$user->student_id = $student->id;
				}
			} else {
				$student = [
					'school_id'   => $data['school_id'],
					'type'        => UserType::Student,
					'name'        => $data['name'],
					'dob'         => $data['dob'],
					'gender'      => $data['gender'],
					'grade'       => $data['grade'],
					'address'     => $data['address'],
					'city'        => $data['city'],
					'state'       => $data['state'],
					'mobile_no'   => $data['mobile_no'],
					'user_id'     => $userID,
					'is_register' => 1,
				];

				$studentID = Students::insertGetId($student);
				$user->student_id = $studentID;
			}

			$user->accessToken = $user->createToken('LifeApp')->accessToken;

			return new JsonResponse(
				['message' => 'Student Register Successfully', 'user' => new UserResource($user)], Response::HTTP_OK
			);
		}

	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function checkUsername(Request $request): JsonResponse
	{
		return new JsonResponse([
				'available' => !User::where('username', $request->get('username', null))->exists()
			],
			Response::HTTP_OK
		);
	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function updateUserName(Request $request): JsonResponse
	{
		$data = $request->validate([
            'username' => 'required|string|unique:users,username'
        ]);
		$user = Auth::user();
        $user->username = $data['username'];
        $user->save();
		return new JsonResponse(['message' => true], Response::HTTP_OK);
	}

	/**
	 * @param UpdatePinRequest $request
	 *
	 * @return JsonResponse
	 */
	public function updatePin(UpdatePinRequest $request): JsonResponse
	{
		$data = $request->validated();
		$user = Auth::user();
		$user->pin = Hash::make($data['pin']);
		$user->update();
		return new JsonResponse(['message' => 'Pin update Successfully', 'user' => new UserResource($user)] , Response::HTTP_OK);
	}

	/**
	 * @param MediaRequest $request
	 *
	 * @return JsonResponse
	 */
	public function updateProfileImage(MediaRequest $request): JsonResponse
	{
		$validatedData = $request->validated();
		$media = $validatedData['media'];
		$mediaName = $media->getClientOriginalName();
		$mediaPath = Storage::put('s3', $media);
		$user = Auth::user();
		$user->profile_image = $mediaName;
		$user->image_path = $mediaPath;
		$user->update();
		return new JsonResponse(['User' => new MediaResource($user)], Response::HTTP_OK);
	}

	/**
	 * @param SignUpRequest $request
	 *
	 * @return JsonResponse
	 */
	public function updateProfile(SignUpRequest $request): JsonResponse
	{
		$data = $request->validated();
		$user = $request->user();

		if ($request->has('school')) {
		    $school = School::firstOrCreate(['name' => $request->get('school')]);
		    $data['school_id'] = $school->id;
        }

		$user->update($data);

		return new JsonResponse([
		    'message' => 'Profile Update Successfully',
            'User' => new UserResource($user)
        ], Response::HTTP_OK);
	}
}
