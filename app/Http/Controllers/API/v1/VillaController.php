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
use App\Models\Date;
use App\Models\Detail;
use App\Models\Image;
use App\Models\Info;
use App\Models\ReservedDate;
use App\Models\Rule;
use App\Models\Search;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image as ImageEditor;

class VillaController extends Controller
{

    public function show($id)
    {
        $villa = Villa::find($id);
        if ($villa) {
            return new VillaResource($villa);
        }
        return response()->json(['status' => 404, 'data' => 'villa not found', 'error' => true]);
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
        $customizedDates = Villa::where('id', $id)->first()->dates;
        return new VillaReservedDatesCollection($reservedDates, $customizedDates);
    }

    public function villaPrices($id)
    {

        $rules = Rule::where('villa_id', $id)->get(['normal_cost', 'special_cost']);
        $custom_dates = Date::where([['villa_id', $id], ['status', 0]])->get(['date', 'status', 'special_price']);

        $v1 = verta();
        $v2=verta('+1 month');
        $data = [];
        $current_day=$v1->day;

        $current_month = $v1->month;
        $current_month_days = $v1->daysInMonth;
        
        $last_month = $v2->month;
        $last_month_days = $v2->daysInMonth;

        $current_year = $v1->year;
        $last_year=$v2->year;


        $data[0]['year']=$current_year;
        $data[0]['month']=$current_month;

        $data[1]['year']=$last_year;
        $data[1]['month']=$last_month;

        $data[0]['daysPrice']=[];
        $data[1]['daysPrice']=[];


    
        for ($i=$current_day; $i <= $current_month_days; $i++) {    
            if( 
             (verta()->startMonth()->addDays($i-1)->isFriday()) or
             (verta()->startMonth()->addDays($i-1)->isThursday()) 
            ){
                array_push($data[0]['daysPrice'],$rules[0]->special_cost);
            }else{
                array_push($data[0]['daysPrice'],$rules[0]->normal_cost);
            }
        }
        

        $next_month_data=$current_month_days-$current_day+1;
        for ($i2 =$next_month_data ; $i2 <= $last_month_days+$next_month_data; $i2++) {
            if( (verta('+'.$i2.' day')->isFriday()) or (verta('+'.$i2.' day')->isThursday()) ){
                array_push($data[1]['daysPrice'],$rules[0]->special_cost);
            }else{
                array_push($data[1]['daysPrice'],$rules[0]->normal_cost);
            }
        }


    foreach ($data[0]['daysPrice'] as $key2 => $value2) {
        foreach ($custom_dates as $key3 => $value3) {
            if ((verta('+'.$key2.' day')->format('Y/n/j')) 
                == 
                (Verta::instance($value3->date)->format('Y/n/j')))
            {
                $data[0]['daysPrice'][$key2] = $value3->special_price;
            }
        }
    }

    foreach ($data[1]['daysPrice'] as $key4 => $value4) {
        foreach ($custom_dates as $key5 => $value5) {
            if (
                (verta('+'.($key4+$next_month_data).' day')->format('Y/n/j')) 
                == 
                (Verta::instance($value5->date)->format('Y/n/j'))
               )
            {
                $data[1]['daysPrice'][$key4] = $value5->special_price;
            }
        }
    }


        array_pop($data[1]['daysPrice']);
        return response()->json(['data' =>$data]);
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

        DB::transaction(function () use ($request, $user) {
            $villaId = $this->saveVillaData($request, $user);
            $details = $this->saveVillaDetails($request, $villaId);
            $info = $this->saveVillaInfo($request, $villaId);
            $rules = $this->saveVillaRules($request, $villaId);
            $images = $this->saveVillaImages($request, $villaId);
            return response()->json(['data' => 'Villa created successfully']);
        });
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
            'disinfected' => 'required',
            'normal_cost' => 'required|numeric'
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
}
