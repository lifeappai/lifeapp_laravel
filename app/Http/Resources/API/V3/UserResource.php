<?php

namespace App\Http\Resources\API\V3;

use App\Enums\UserType;
use App\Http\Resources\SchoolResource;
use App\Models\TeacherScoreSnapshot;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->image_path) {
            $media = $this->image_path;
        }

        // Default badge
        $engagementBadge = null;

        // If user is a teacher, fetch badge for current month
        if ($this->type == UserType::Teacher) {
            $currentMonth = Carbon::now()->format('Y-m');
            $snapshot = TeacherScoreSnapshot::where('user_id', $this->id)
                ->where('month', $currentMonth)
                ->first();

            $engagementBadge = $snapshot?->engagement_badge;
        }
        
        $user = [
            'id' => $this->id ?? null,
            // 'student_id' => $this->student_id ?? null,
            'school' => $this->school ? new SchoolResource($this->school) : null,
            'section' => $this->laSection ? new LaSectionResource($this->laSection) : null,
            'name' => $this->name ?? null,
            'guardian_name' => $this->guardian_name ?? null,
            'username' => $this->username ?? null,
            'mobile_no' => $this->mobile_no ?? null,
            'dob' => $this->dob ?? null,
            'la_board_id' => $this->la_board_id,
            'board_name' => $this->board_name,
            'board' => $this->whenLoaded('laBoard', function() {  // Add board relationship
                return new LaBoardResource($this->laBoard);
            }),
            'gender' => $this->gender,
            'grade' => $this->laGrade ? new LaGradeResource($this->laGrade) : null,
            'city' => $this->city,
            'state' => $this->state ?? null,
            'address' => $this->address ?? null,
            'image_path' => $media ?? null,
            'profile_image' => $this->profile_image ?? null,
            'mission_completes' => $this->laMissionApproved->count(),
            'quiz' => $this->laQuizGameResults->count(),
            'friends' => $this->friends->count() + $this->friendsAccept->count(),
            'earn_coins' => $this->earn_coins,
            'subjects' => (count($this->mentorSubjects) > 0) ? MentorSubjectResource::collection($this->mentorSubjects) : null,
            'school_code' => $this->school_code,
            'user_rank' => $this->user_rank,
            'mentor_code' => $this->pin ?? null,
            'unread_notification_count' => isset(auth()->user()->unreadNotifications) ? auth()->user()->unreadNotifications->groupBy('notifiable_type')->count() : null,
            'engagement_badge' => $engagementBadge,
        ];

        if ($this->type == UserType::Teacher){
            $user['la_teacher_grades'] = $this->laTeacherGrades ? LaTeacherGradeResource::collection($this->laTeacherGrades) : '';
        }

        $token = $this->accessToken ? ['token' => $this->accessToken] : null;
        if (!empty($token)) {
            $user = array_merge($user, $token);
        }
        $baloonCarMission = $this->baloonCarMission ? ['baloonCarMission' => $this->baloonCarMission] : ['baloonCarMission' => null];
        $user = array_merge($user, $baloonCarMission);
        return $user;
    }
}
