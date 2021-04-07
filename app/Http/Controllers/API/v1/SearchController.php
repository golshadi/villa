<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Other\PopularVillasCollection;
use App\Http\Resources\v1\Other\SearchCollection;
use App\Models\Detail;
use App\Models\ReservedDate;
use App\Models\Rule;
use App\Models\Search;
use App\Models\Villa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Verta;

class SearchController extends Controller
{
    public function search()
    {
        $data = Villa::orderBy('id', 'desc')->paginate(2);
        return new PopularVillasCollection($data);
    }


    public function testSearch(Request $request)
    {

        $orderField = 'id';
        $orderType = 'desc';
        if (isset($_GET['orderBy'])) {
            $orderBy = $_GET['orderBy'];
            $orderField = $orderBy;
            if (($orderBy == 'cost') or ($orderBy == 'area')) {
                $orderType = 'asc';
            }
        }

        $area = '';
        if (isset($_GET['area'])) {
            $area = $_GET['area'];
        }

        $type = [];
        if (isset($_GET['type'])) {
            $typeParametr = explode(',', $_GET['type']);
            foreach ($typeParametr as $key => $value) {
                $type[$key] = $value;
            }
        }

        $bedroom = 0;
        if (isset($_GET['bedroom'])) {
            $bedroom = $_GET['bedroom'];
        }


        $costRange = [];
        if (isset($_GET['costRange'])) {
            $costRange = explode(',', $_GET['costRange']);
        }
        $min_cost = 0;
        if (isset($_GET['costRange'])) {
            $min_cost = $costRange[0]+0;
        }
        $max_cost = 99999999;
        if (isset($_GET['costRange'])) {
            $max_cost = $costRange[1]+0;
        }



        $dateRange = [];
        if (isset($_GET['dateRange'])) {
            $dateRange = explode(',', $_GET['dateRange']);
            $min_dates = $dateRange[0];
            $min_dates = explode('/', $min_dates);
        }

        $min_date = '1300/1/1';
        if (isset($_GET['dateRange'])) {
            $min_date = str_replace('/', '-', implode(',', Verta::getGregorian(
                $min_dates[0],
                $min_dates[1],
                $min_dates[2]
            )));
            $min_date = str_replace(',', '-', $min_date);
        }
        $max_date = '2000/1/1';
        if (isset($_GET['dateRange'])) {
            $max_cost = $dateRange[1];
        }


        $passengers_count = 0;
        if (isset($_GET['passengers_count'])) {
            $passengers_count =  $_GET['passengers_count'];
        }

        $data = [];
        // if ((isset($_GET['type'])) or (isset($_GET['passengers']))) {
        //     $data = Villa::when(isset($_GET['area']), function ($query) use ($area) {
        //         $query->where([['city', $area], ['village', $area]]);
        //     })
        //         ->when(isset($_GET['type']), function ($query) use ($type) {
        //             $query->whereIn('type', $type);
        //         })
        //         ->when(isset($_GET['passengers']), function ($query) use ($passengers) {
        //             $query->join('villa_details', 'villas.id', '=', 'villa_details.villa_id')
        //                 ->where('villa_details.max_capacity', '>=', $passengers);
        //         })
        //         ->when(isset($_GET['bedroom']), function ($query) use ($bedroom) {
        //             $query->join('villa_rules', 'villas.id', '=', 'villa_rules.villa_id')
        //                 ->where('villa_rules.bedroom', '>=', $bedroom);
        //         })
        //         ->when(isset($_GET['costRange']), function ($query) use ($min_cost, $max_cost) {
        //             $query->join('villa_rules', 'villas.id', '=', 'villa_rules.villa_id')
        //                 ->whereBetween('villa_rules.normal_cost', [$min_cost, $max_cost]);
        //         })
        //         ->when(isset($_GET['dateRange']), function ($query) use ($min_date) {
        //             $query->join('villa_reservation', 'villas.id', '=', 'villa_reservation.villa_id')
        //                 ->where('villa_reservation.end_date', '>=', $min_date);
        //         })
        //         ->orderBy($orderField, $orderType)
        //         ->get();
        // }

        $test = ReservedDate::where('end_date', '>=', $min_date)->get();

        $res = Search::when(isset($_GET['area']), function ($query) use ($area) {
            $query->where([['city', $area], ['village', $area]]);
        })
            ->when(isset($_GET['type']), function ($query) use ($type) {
                $query->whereIn('category', $type);
            })
            ->where([
                ['passengers_count', '>=', $passengers_count],
                ['bedroom', '>=', $bedroom]
            ])
            ->WhereBetween('normal_cost', [$min_cost, $max_cost])
            // ->orderBy($orderField, $orderType)
            ->with('villa')
            ->get();

            // return $res;
        return new SearchCollection($res);
        // return [$min_cost,$max_cost];
    }
}
