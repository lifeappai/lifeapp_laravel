<?php

namespace App\Http\Resources\API\V3;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\V1\MediaResource;
use App\Models\Vision;
use App\Models\VisionAssign;
use App\Models\VisionQuestionAnswer;
use App\Models\VisionUserStatus;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserType;

class LaVisionResource extends JsonResource
{
    public function toArray($request)
    {
        $youtubeUrl = trim($this->youtube_url);
        $videoId = $this->getYoutubeVideoId($this->youtube_url);
        $assignedCount = VisionAssign::where('vision_id', $this->id)->count();

        $submittedCount = VisionQuestionAnswer::where('vision_id', $this->id)
            ->distinct('user_id') 
            ->count('user_id');

        return [
            'id' => $this->id,
            'title' => json_decode($this->title, true)['en'] ?? null,
            'description' => json_decode($this->description, true)['en'] ?? null,
            'youtubeUrl' => $videoId ? "https://www.youtube.com/watch?v={$videoId}" : null,
            'thumbnailUrl' => $this->thumbnail_url ?? "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg",
            'status' => VisionUserStatus::where('user_id', auth()->id())
                ->where('vision_id', $this->id)
                ->value('status'),
            'teacherAssigned' => $this->assignments()
                ->where('student_id', auth()->id())
                ->exists() &&
                VisionUserStatus::where('user_id', auth()->id())
                    ->where('vision_id', $this->id)
                    ->where('status', ['completed', 'submitted'])
                    ->doesntExist(),
            'assigned_count' => $assignedCount,
            'submitted_count' => $submittedCount,

            // Optional Enhancements:
            'level' => $this->laLevel ? new LaLevelResource($this->laLevel) : [],
            'subject' => $this->subject ? [
                'id' => $this->subject->id,
                'title' => $this->subject->title,
            ] : null,
            'chapters' => $this->chapters->map(function ($chapter) {
                return [
                    'id' => $chapter->id,
                    'title' => $chapter->title,
                ];
            }),
            'questionsCount' => $this->questions_count,
            'assigned_by' => optional(optional($this->assignments()->first())->teacher)->name,

            'assignments' => $this->visionAssigns->map(function ($assign) {
                return [
                    'student_id' => $assign->student_id,
                    'student_name' => optional($assign->student)->name,
                    'due_date' => $assign->due_date,
                ];
            }),
        ];
    }

    private function getYoutubeVideoId($url)
    {
        $url = trim($url);
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|shorts\/)|youtu\.be\/)([^&\n\/]+)/', $url, $matches)) {
            $videoId = trim($matches[1]);

            $videoId = explode('?', $videoId)[0];

            return $videoId;
        }
        return '';
    }


}
