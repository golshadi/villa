<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Other\PopularVillasCollection;
use App\Http\Resources\v1\Other\SearchCollection;
use App\Models\ReservedDate;
use App\Models\Search;
use App\Models\Villa;
use Illuminate\Http\Request;
use Hekmatinasser\Verta\Verta;

class SearchController extends Controller
{
    public function search()
    {
        $data = Villa::orderBy('id', 'desc')->with(['detail', 'rule'])->paginate(2);
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
        if (isset($_GET['bedroom'])) {
            $bedroom = $_GET['bedroom'];
        }


        // Search by costRange parametr => Example : costRange=200,4000
        $costRange = [];
        $min_cost = 0;
        $max_cost = 99999999;
        if (isset($_GET['costRange'])) {
            $costRange = explode(',', $_GET['costRange']);
            $min_cost = $costRange[0] + 0;
            $max_cost = $costRange[1] + 0;
        }


        // Search by dateRange parametr => Example : dateRange=1400/1/1,1400/1/5
        $dateResult = [];
        if (isset($_GET['dateRange'])) {
            $dateResult = $this->searchByDate($_GET['dateRange']);
        }


        // Search by passengers_count parametr => Example : passengers_count=5
        $passengers_count = 0;
        if (isset($_GET['passengers_count'])) {
            $passengers_count =  $_GET['passengers_count'];
        }


        // Search by discount parametr => Example : discount=1
        $discount = 0;
        if (isset($_GET['discount'])) {
            $discount =  1;
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

        $result = Search::when(isset($_GET['area']), function ($query) use ($areaType, $area) {
            $query->where($areaType, 'LIKE', "%" . $area . "%");
        })
            ->when(isset($_GET['type']), function ($query) use ($type) {
                $query->whereIn('category', $type);
            })
            ->when(isset($_GET['dateRange']), function ($query) use ($dateResult) {
                $query->whereNotIn('villa_id', $dateResult);
            })
            ->when(isset($_GET['discount']), function ($query) use ($discount) {
                $query->where('discount', $discount);
            })
            ->when(isset($_GET['disinfected']), function ($query) use ($disinfected) {
                $query->where('disinfected', $disinfected);
            })
            ->where('passengers_count', '>=', $passengers_count)
            ->where('bedroom', '>=', $bedroom)
            ->WhereBetween('normal_cost', [$min_cost, $max_cost])
            ->orderBy($orderField, $orderType)
            ->with(['villa', 'detail', 'rule'])
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

        $dateResult = ReservedDate::whereBetween('start_date', [$min_date, $max_date])
            ->orWhereBetween('end_date', [$min_date, $max_date])->pluck('villa_id')->toArray();
        return $dateResult;
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