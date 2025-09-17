<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Traits\MediaUpload;
use App\Models\Coupon;
use App\Models\CouponRedeem;
use App\Models\School;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CouponController extends Controller
{
    use MediaUpload;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $schoolName = $request->school_name;

        $coupons = Coupon::with('school') // Load the school relationship
            ->when($schoolName, function ($query, $schoolName) {
                $query->whereHas('school', function ($q) use ($schoolName) {
                    $q->where('name', 'LIKE', '%' . $schoolName . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(25);

        $schools = School::select('id', 'name')->get(); // For dropdown or autocomplete
        $imageBaseUrl = $this->getBaseUrl();

        return view('pages.admin.coupons.index', compact('coupons',
        'schoolName', 'imageBaseUrl'));
    }

    public function searchSchools(Request $request)
    {
        $query = $request->get('query');
        
        $schools = School::where('name', 'LIKE', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'name']);
        
        return response()->json($schools);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    
    
    
     public function create()
    {
        return view('pages.admin.coupons.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $media = $this->upload($request->image);
        $data['coupon_media_id'] = $media->id;
        Coupon::create($data);
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupon $coupon)
    {
        $imageBaseUrl = $this->getBaseUrl();
        return view('pages.admin.coupons.edit', compact('coupon', 'imageBaseUrl'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coupon $coupon)
    {
        $data = $request->except(['_method', '_token']);
        if ($request->image) {
            $media = $this->upload($request->image);
            $data['coupon_media_id'] = $media->id;
        }
        Coupon::find($coupon->id)->update($data);
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon Updated');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return response()->json(["status" => 200, "message" => "Coupon Deleted"]);
    }


    
    public function indexChange(Coupon $coupon, Request $request)
    {
        $index = $request->index;
        if (!$index) {
            $index = 1;
        }
        $coupon->index = $index;
        $coupon->update();
        return response()->json(["status" => 200, "message" => "Coupon Index Changed", "index" => $index]);
    }
}
