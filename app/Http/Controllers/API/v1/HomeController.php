<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Other\DiscountedVillasCollection;
use App\Http\Resources\v1\Other\EconomicVillasCollection;
use App\Http\Resources\v1\Other\PopularVillasCollection;
use App\Models\Banner;
use App\Models\Rule;
use App\Models\Villa;

class HomeController extends Controller
{

    public function popularVillas()
    {
        $data = Villa::orderBy('visit_count', 'desc')->with(['detail','rule'])->take(4)->get();
        return new PopularVillasCollection($data);
    }


    public function getBanners()
    {
        $data = Banner::all();
        return $this->bannersType($data);
    }

    public function bannersType($data){
        $bannerType1 = [];
        $bannerType2 = [];
        $bannerType3 = [];

        foreach ($data as $key => $item) {
            switch ($item->type) {
                case 1:
                    array_push($bannerType1,$item);
                    break;
                case 2:
                    array_push($bannerType2,$item);
                    break;
                case 3:
                    array_push($bannerType3,$item);
                    break;
            }
        }
        return ['villages'=>$bannerType1,'villas'=>$bannerType2,'bigBanners'=>$bannerType3];
    }

    
    public function discountedVillas()
    {
        $data = Rule::where('weekly_discount', '>', 0)
        ->orderBy('id', 'desc')
        ->with('villa')
        ->take('4')->get();
        return new DiscountedVillasCollection($data);
    }


    public function economicVillas(){
        $data = Rule::where('weekly_discount', '>', 0)
        ->orwhere('monthly_discount', '>', 0)->orderBy('normal_cost', 'asc')
        ->with('villa')
        ->take('4')->get();
        return new EconomicVillasCollection($data);
    }

}