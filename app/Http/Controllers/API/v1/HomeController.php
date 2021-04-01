<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\DiscountedVillasCollection;
use App\Http\Resources\v1\EconomicVillasCollection;
use App\Http\Resources\v1\PopularVillasCollection;
use App\Models\Banner;
use App\Models\Rule;
use App\Models\Villa;

class HomeController extends Controller
{

    public function popularVillas()
    {
        $data = Villa::orderBy('visit_count', 'desc')->take(4)->get();
        return new PopularVillasCollection($data);
    }

    public function getBanners()
    {
        $data = Banner::all();
        $bannerType1 = [];
        $bannerType2 = [];
        $bannerType3 = [];

        foreach ($data as $key => $item) {
            switch ($item->type) {
                case 1:
                     $bannerType1[$key] = $item;
                    break;
                case 2:
                     $bannerType2[$key] = $item;
                    break;
                case 3:
                     $bannerType3[$key] = $item;
                    break;
            }
        }

        return response()->json(['type1'=>$bannerType1,'type2'=>$bannerType2,'type3'=>$bannerType3]);
    }
    
    public function discountedVillas()
    {
        $data = Rule::where('weekly_discount', '>', 0)->orwhere('monthly_discount', '>', 0)->orderBy('id', 'desc')->take('4')->get();
        return new DiscountedVillasCollection($data);
    }

    public function economicVillas(){
        $data = Villa::orderBy('id', 'desc')->take(4)->get();
        return new EconomicVillasCollection($data);
    }

}