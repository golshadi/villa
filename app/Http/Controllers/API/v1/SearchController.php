<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Other\PopularVillasCollection;
use App\Http\Resources\v1\Other\SearchCollection;
use App\Models\Date;
use App\Models\Detail;
use App\Models\ReservedDate;
use App\Models\Rule;
use App\Models\Villa;
use Illuminate\Http\Request;
use Hekmatinasser\Verta\Verta;

class SearchController extends Controller
{
    public function search()
    {
        $data = Villa::orderBy('id', 'desc')->with(['detail', 'rule'])->paginate(9);
        return new PopularVillasCollection($data);
    }

    public function doSearch(Request $request)
    {

        // Search by type parametr => Example : area=Cمازندران  or area=Vنلاس
        // C=city & V=village
        $area = '';
        $areaType = '';
        if (isset($_GET['area'])) {
            $searchByArea = $this->searchByArea($_GET['area']);
            $areaType = $searchByArea[0];
            $area = $searchByArea[1];
        }

        // Search by type parametr => Example : type=ویلایی
        $type = [];
        if (isset($_GET['type'])) {
            $typeParametr = explode(',', $_GET['type']);
            foreach ($typeParametr as $key => $value) {
                $type[$key] = $value;
            }
        }

        // Search by bedroom parametr => Example : bedroom=2
        $bedroom = 0;
        $bedroomData=[];
        if (isset($_GET['bedroom'])) {
            $bedroom = $_GET['bedroom'];
            $bedroomData=Detail::where('bedroom','>=',$bedroom)->pluck('villa_id')->toArray();
        }


        // Search by costRange parametr => Example : costRange=200,4000
        $costRange = [];
        $min_cost = 0;
        $max_cost = 99999999;
        $costRanges=[];
        if (isset($_GET['costRange'])) {
            $costRange = explode(',', $_GET['costRange']);
            $min_cost = $costRange[0] + 0;
            $max_cost = $costRange[1] + 0;
            $costRanges = Rule::WhereBetween('normal_cost', [$min_cost, $max_cost])->pluck('villa_id')->toArray();
        }


        // Search by dateRange parametr => Example : dateRange=1400/1/1,1400/1/5
        $dateResult = [];
        if (isset($_GET['dateRange'])) {
            $dateResult = $this->searchByDate($_GET['dateRange']);
        }


        // Search by passengers_count parametr => Example : passengers_count=5
        $passengers_count = 0;
        $passengersData=[];
        if (isset($_GET['passengers_count'])) {
            $passengers_count =  $_GET['passengers_count'];
            $passengersData=Detail::where('standard_capacity','>=',$passengers_count)->pluck('villa_id')->toArray();
        }


        // Search by discount parametr => Example : discount=1
        $discount = 0;
        $discountedVillas=[];
        if (isset($_GET['discount'])) {
            // $discount =  1;
            $discountedVillas = Rule::where('weekly_discount','>',0)
            ->orWhere('monthly_discount','>',0)->pluck('villa_id')->toArray();
        }

        // Search by disinfected parametr => Example : disinfected=1
        $disinfected = 0;
        if (isset($_GET['disinfected'])) {
            $disinfected =  1;
        }


        // Search by orderFiled parametr => Example : orderField=Expensive
        $orderField = 'id';
        $orderType = 'desc';
        if (isset($_GET['orderBy'])) {
            $searchByOrdering = $this->searchByOrdering($_GET['orderBy']);
            $orderField = $searchByOrdering[0];
            $orderType = $searchByOrdering[1];
        }

        $result = Villa::when(isset($_GET['area']), function ($query) use ($areaType, $area) {
                $query->where($areaType, 'LIKE', "%" . $area . "%");
             })
            ->when(isset($_GET['type']), function ($query) use ($type) {
                $query->whereIn('type', $type);
            })
            ->when(isset($_GET['disinfected']), function ($query) use ($disinfected) {
                $query->where('disinfected', $disinfected);
            })
            ->when(isset($_GET['discount']), function ($query) use ($discountedVillas) {
                $query->whereIn('id', $discountedVillas);
            })
            ->when(isset($_GET['passengers_count']), function ($query) use ($passengersData) {
                $query->whereIn('id', $passengersData);
            })
            ->when(isset($_GET['bedroom']), function ($query) use ($bedroomData) {
                $query->whereIn('id', $bedroomData);
            })
            ->when(isset($_GET['costRange']), function ($query) use ($costRanges) {
                $query->whereIn('id', $costRanges);
            })
            ->when(isset($_GET['dateRange']), function ($query) use ($dateResult) {
                $query->whereNotIn('id', $dateResult);
            })
            ->orderBy($orderField, $orderType)
            ->with(['detail', 'rule'])
            ->paginate(9);

        return new SearchCollection($result);
    }

    public function searchByOrdering($orderBy)
    {

        $orderField = $orderBy;
        switch ($orderField) {
            case 'Newest':
                $orderField = 'id';
                $orderType = 'desc';
                break;
            case 'Expensive':
                $orderField = 'normal_cost';
                $orderType = 'desc';
                break;
            case 'Cheapest':
                $orderField = 'normal_cost';
                $orderType = 'asc';
                break;
            case 'Popular':
                $orderField = 'score';
                $orderType = 'desc';
                break;
            case 'Nearset':
                $orderField = 'city';
                $orderType = 'asc';
                break;
            default:
                $orderField = 'id';
                $orderType = 'desc';
                break;
        }

        return [$orderField, $orderType];
    }

    public function searchByDate($recievedDate)
    {

        $dateRange = explode(',', $recievedDate);
        $min_dates = $dateRange[0];
        $min_dates = explode('/', $min_dates);
        $max_dates = $dateRange[1];
        $max_dates = explode('/', $max_dates);

        $min_date = str_replace('/', '-', implode(',', Verta::getGregorian(
            $min_dates[0],
            $min_dates[1],
            $min_dates[2]
        )));
        $min_date = str_replace(',', '-', $min_date);

        $max_date = str_replace('/', '-', implode(',', Verta::getGregorian(
            $max_dates[0],
            $max_dates[1],
            $max_dates[2]
        )));
        $max_date = str_replace(',', '-', $max_date);

        $dateResult1 = ReservedDate::whereBetween('start_date', [$min_date, $max_date])
            ->orWhereBetween('end_date', [$min_date, $max_date])->pluck('villa_id')->toArray();

        $dateResult2 = Date::where('status', 1)->whereBetween('date', [$min_date, $max_date])
            ->pluck('villa_id')->toArray();

        return array_merge($dateResult1, $dateResult2);
    }

    public function searchByArea($recivedArea)
    {
        $area = mb_substr($recivedArea, 2, null, mb_detect_encoding($recivedArea));
        $areaType = mb_substr($recivedArea, 0, 1, mb_detect_encoding($recivedArea));
        if ($areaType == 'C') {
            $areaType = 'city';
        } else {
            $areaType = 'village';
        }
        return [$areaType, $area];
    }
}