<?php

namespace App\Http\Resources\API\V3;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\VisionQuestionAnswer;

class LaVisionAssignResource extends JsonResource
{
    public function toArray($request)
    {
        $completion = VisionQuestionAnswer::where('user_id', $this->student_id)
            ->where('vision_id', $this->vision_id)
            ->latest()
            ->first();

        $status = 'Assigned';
        if ($completion) {
            switch ($completion->status) {
                case 'requested':
                    $status = 'Submitted (Pending)';
                    break;
                case 'approved':
                    $status = 'Approved';
                    break;
                case 'rejected':
                    $status = 'Rejected';
                    break;
            }
        }

        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'student_name' => optional($this->student)->name,
            'student_profile_image' => optional($this->student)->profile_image_url ?? null,

            'assigned_at' => optional($this->created_at)->format('Y-m-d H:i'),
            'due_date' => optional($this->due_date)->format('Y-m-d'),

            'submission_status' => $status,
            'submitted_at' => $completion ? $completion->created_at->format('Y-m-d H:i') : null,
            'remarks' => $completion ? $completion->remarks : null,

            'is_submitted' => in_array($status, ['Submitted (Pending)', 'Approved', 'Rejected']),
            'is_pending_review' => $status === 'Submitted (Pending)',
            'is_approved' => $status === 'Approved',
            'is_rejected' => $status === 'Rejected',

            'grade' => optional($this->student->grade)->name ?? null,
            'section' => optional($this->student->section)->name ?? null,
        ];
    }
}
