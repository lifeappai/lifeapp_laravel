<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V3\FaqResource;
use App\Models\LaFaq;
use App\Models\LaFaqCategory;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * GET /api/faq
     * Returns FAQs for the currently logged in user
     */
   public function index(Request $request)
    {
        $user = $request->user();

        // Get filters
        $audience = $user->isTeacher() ? 'teacher' : 'student';
        $topicId = $request->get('category_id'); // optional filter from app

        $faqs = LaFaq::query()
            ->when($topicId, fn($q) => $q->where('category_id', $topicId))
            ->where(function ($q) use ($audience) {
                $q->where('audience', 'all')
                ->orWhere('audience', $audience);
            })
            ->where('is_active', 1)
            ->with('category')
            ->orderBy('updated_at', 'desc')
            ->get();

        return FaqResource::collection($faqs);
    }

}
