<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Other\PopularVillasCollection;
use App\Models\Villa;
use Illuminate\Http\Request;
use App\Http\Resources\v1\Villa\Villa as VillaResource;
use App\Http\Resources\v1\Villa\VillaDatesCollection;
use App\Http\Resources\v1\Villa\VillaImages;
use App\Http\Resources\v1\Villa\VillaReservedDatesCollection;
use App\Models\Detail;
use App\Models\Image;
use App\Models\Info;
use App\Models\ReservedDate;
use App\Models\Rule;
use App\Models\Search;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image as ImageEditor;

class VillaController extends Controller
{

    public function show($id)
    {
        $villa = Villa::findOrFail($id);
        return new VillaResource($villa);
    }


    public function images($id)
    {
        $images = Villa::findorFail($id)->images;
        return new VillaImages($images);
    }

    public function dates($id)
    {
        $dates = Villa::findOrFail($id)->dates;
        return new VillaDatesCollection($dates);
    }

    public function reservedDates($id)
    {
        $reservedDates = ReservedDate::where([['villa_id', $id], ['status', 2]])->get();
        $customizedDates= Villa::where('id',$id)->first()->dates;
        return new VillaReservedDatesCollection($reservedDates,$customizedDates);
    }

    public function similarVillas($id)
    {
        $villaState = Villa::findOrFail($id)->state;
        $similarVillas = Villa::where([['state', $villaState], ['id', '!=', $id]])
            ->orderBy('id', 'desc')->take(4)->get();
        return new PopularVillasCollection($similarVillas);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $villaId = $this->saveVillaData($request, $user);
        $details = $this->saveVillaDetails($request, $villaId);
        $info = $this->saveVillaInfo($request, $villaId);
        $rules = $this->saveVillaRules($request, $villaId);
        $images = $this->saveVillaImages($request, $villaId);
        $search = $this->saveSearchData([
            $request->city, $request->village, $request->max_capacity, $request->bedroom, $request->normal_cost,
            $request->type, $request->weekly_discount, $request->monthly_discount, $request->disinfected
        ], $villaId);

        return response()->json(['data' => 'Done successfully']);
    }


    public function saveVillaData($request, $user)
    {
        $villaValidation = $this->validate($request, [
            'title' => 'required',
            'type' => 'required',
            'phone_number' => 'required|max:11|min:11',
            'story' => 'required',
            'state' => 'required',
            'city' => 'required',
            'village' => 'max:80',
            'postal_code' => 'max:10|min:10|required',
            'address' => 'required',
            'long' => 'required',
            'lat' => 'required',
            'disinfected' => 'required'
        ]);
        $villaValidation['user_id'] = $user->id;
        $villa = Villa::updateOrCreate([
            'postal_code' => $request->postal_code,
            'phone_number' => $request->phone_number
        ], $villaValidation);
        return $villa->id;
    }

    public function saveVillaDetails($request, $villaId)
    {
        $detailsValidation = $this->validate($request, [
            'standard_capacity' => 'required|numeric',
            'max_capacity' => 'required|numeric',
            'rent_type' => 'required',
            'bedroom' => 'required|numeric',
            'ir_toilet' => 'required|numeric',
            'eu_toilet' => 'required|numeric',
            'shower' => 'required|numeric',
            'shared_bathroom' => 'required|numeric',
            'places' => 'required',
            'view' => 'required',
            'area' => 'required',
        ]);
        $detailsValidation['villa_id'] = $villaId;
        $details = Detail::updateOrCreate([
            'villa_id' => $villaId
        ], $detailsValidation);
    }

    public function saveVillaInfo($request, $villaId)
    {
        $infoValidation = $this->validate($request, [
            'general_fac' => 'required',
            'kitchen_fac' => 'required',
            'temp_fac' => 'required',
            'chef' => 'max:30',
            'host' => 'max:30',
            'tour_guide' => 'max:30',
            'bodyguard' => 'max:30',
            'catering' => 'max:50'
        ]);
        $infoValidation['villa_id'] = $villaId;
        $info = Info::updateOrCreate([
            'villa_id' => $villaId
        ], $infoValidation);
    }

    public function saveVillaRules($request, $villaId)
    {
        $rulesValidation = $this->validate($request, [
            'normal_cost' => 'required|numeric',
            'special_cost' => 'numeric',
            'normal_extra_cost' => 'required|numeric',
            'special_extra_cost' => 'numeric',
            'weekly_discount' => 'numeric',
            'monthly_discount' => 'numeric',
            'auth_rules' => 'required',
            'special_rules' => 'max:200',
            'min_reserve' => 'required|numeric',
            'max_reserve' => 'required|numeric',
            'suitable_for' => 'required',
            'arrival_time' => 'required',
            'exit_time' => 'required',
        ]);
        $rulesValidation['villa_id'] = $villaId;
        $rules = Rule::updateOrCreate([
            'villa_id' => $villaId
        ], $rulesValidation);
    }

    public function saveVillaImages($request, $villaId)
    {
        $data = [];
        $villa = Villa::findOrFail($villaId);

        if ($request->hasFile('images')) {
            $this->validate($request, [
                'images' => 'max:2048|mimes:jpg,png,bmp,jpeg|image'
            ]);
            $images = $request->file('images');
            Image::where('villa_id', $villaId)->delete();
            foreach ($images as $key => $image) {
                $name = 'Villa-' . time() . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
                if ($images[0] == $image) {
                    $image->move('images/villas/main', $name);
                    $img = ImageEditor::make('images/villas/main/' . $name)->fit(278, 228);
                    $img->save('images/villas/thum/' . $name);
                    $villa->update(['main_img' => $name]);
                } else {
                    $image->move(public_path('images/villas/main'), $name);
                }
                $data[$key] = ['villa_id' => $villa->id, 'img_src' => $name, 'img_title' => $request->img_title[$key]];
            }
        }
        Image::insert($data);
    }

    public function saveSearchData($request, $villaId)
    {
        $discount = 0;
        if (($request[6] > 0) or ($request[7] > 0)) {
            $discount = 1;
        }
        Search::updateOrCreate([
            'villa_id' => $villaId
        ], [
            'villa_id' => $villaId,
            'city' => $request[0],
            'village' => $request[1],
            'max_capacity' => $request[2],
            'bedroom' => $request[3],
            'normal_cost' => $request[4],
            'category' => $request[5],
            'discount' => $discount,
            'disinfected' => $request[8]
        ]);
    }
}
