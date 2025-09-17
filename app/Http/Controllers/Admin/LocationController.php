<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LocationController extends Controller
{
    public function getStates()
    {
        $states = json_decode(file_get_contents(storage_path() . "/india.json"), true);
        return view('pages.admin.state.index', compact('states'));
    }

    public function getCities($id)
    {
        $states = json_decode(file_get_contents(storage_path() . "/india.json"), true);
        $cities = isset($states[$id]) ? $states[$id] : [];
        return view('pages.admin.city.index', compact('cities', 'id'));
    }

    public function addState(Request $request)
    {
        $contactInfo = json_decode(file_get_contents(storage_path() . "/india.json"), true);
        $stateCount = count($contactInfo);
        $stateArr = [];
        foreach ($contactInfo as $states) {
            $stateArr[] = $states['state_name'];
        }
        if (!in_array($request->state_name, $stateArr)) {
            $contactInfo[$stateCount]['state_name'] = $request->state_name;
            $contactInfo[$stateCount]['active'] = 1;
            file_put_contents(storage_path() . "/india.json", json_encode($contactInfo));
            return back()->with('success', 'State added successfully');
        }
        return back()->with('error', 'State is already exists');
    }

    public function addCity($id, Request $request)
    {
        $contactInfo = json_decode(file_get_contents(storage_path() . "/india.json"), true);
        $cities = isset($contactInfo[$id]) ? $contactInfo[$id] : [];
        if (count($cities) > 0) {
            $cities = isset($cities['cities']) ? $cities['cities'] : [];
            $cityCount = count($cities);
            $cityArr = [];
            foreach ($cities as $city) {
                $cityArr[] = $city['city_name'];
            }
            if (!in_array($request->city_name, $cityArr)) {
                $contactInfo[$id]['cities'][$cityCount]['city_name'] = $request->city_name;
                $contactInfo[$id]['cities'][$cityCount]['active'] = 1;
                file_put_contents(storage_path() . "/india.json", json_encode($contactInfo));
                return back()->with('success', 'City added successfully');
            }
        }
        return back()->with('error', 'City is already exists');
    }

    public function updateStatus($id, Request $request)
    {
        $type = $request->type;
        $stateName = $request->state_name;
        $cityName = $request->city_name;
        $contactInfo = json_decode(file_get_contents(storage_path() . "/india.json"), true);

        if ($type == 'city') {

            foreach ($contactInfo as $key => $list) {
                if ($list['state_name'] == $stateName) {
                    if (isset($list['cities'][$id])) {
                        if (isset($contactInfo[$key]['cities'][$id]['active']) && $contactInfo[$key]['cities'][$id]['active'] == 1) {
                            $contactInfo[$key]['cities'][$id]['active'] = 0;
                        } else {
                            $contactInfo[$key]['cities'][$id]['active'] = 1;
                        }
                        break;
                    }
                    break;
                }
            }
            file_put_contents(storage_path() . "/india.json", json_encode($contactInfo));
        } else if ($type == 'state') {
            if ($contactInfo[$id]) {
                if ($contactInfo[$id]['active'] && $contactInfo[$id]['active'] == 1) {
                    $contactInfo[$id]['active'] = 0;
                } else {
                    $contactInfo[$id]['active'] = 1;
                }
            }
            file_put_contents(storage_path() . "/india.json", json_encode($contactInfo));
        }
        return response()->json(['message' => 'active updated successfully']);
    }
}
