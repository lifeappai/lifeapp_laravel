<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Models\SchoolRaw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolRawController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return response()->json([]);
        }

        // Use FULLTEXT search for faster and more relevant results
        $schools = SchoolRaw::select('udise_code', 'school_name', 'district', 'state')
            ->whereRaw("MATCH(school_name) AGAINST(? IN NATURAL LANGUAGE MODE)", [$query])
            ->limit(20)
            ->get();

        // Fallback to LIKE if fulltext returns nothing
        if ($schools->isEmpty()) {
            $schools = SchoolRaw::where('school_name', 'LIKE', "%{$query}%")
                ->limit(50)
                ->get(['udise_code', 'school_name', 'district', 'state']);
        }

        return response()->json($schools);
    }
}
