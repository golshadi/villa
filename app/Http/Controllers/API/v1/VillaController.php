<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Other\PopularVillasCollection;
use App\Models\Villa;
use Illuminate\Http\Request;
use App\Http\Resources\v1\Villa\Villa as VillaResource;
use App\Http\Resources\v1\Villa\VillaComments;
use App\Http\Resources\v1\Villa\VillaDatesCollection;
use App\Http\Resources\v1\Villa\VillaImages;
use App\Http\Resources\v1\Villa\VillaReservedDatesCollection;
use App\Models\Comment;
use App\Models\ReservedDate;

class VillaController extends Controller
{

    public function show($id)
    {
        $villa = Villa::findOrFail($id);
        return new VillaResource($villa);
    }

    public function comments($id)
    {
        $comments = Villa::findorFail($id)->comments;
        $comments_count = Comment::where([['villa_id', $id], ['parent_id', 0]])->count();
        $scores = $this->calcaulateScores($comments, $comments_count);

        return new VillaComments($comments,$scores);
    }


    public function calcaulateScores($comments, $comments_count)
    {

        $cleaning = 0;
        $ad_compliance = 0;
        $hospitality = 0;
        $hosting_quality = 0;

        foreach ($comments as $value) {
            $cleaning += $value->cleaning;
            $ad_compliance += $value->ad_compliance;
            $hospitality += $value->hospitality;
            $hosting_quality += $value->hosting_quality;
        }

        $cleaning = $cleaning / $comments_count;
        $ad_compliance = $ad_compliance / $comments_count;
        $hospitality = $hospitality / $comments_count;
        $hosting_quality = $hosting_quality / $comments_count;

        return ['Cleaning' => $cleaning, 'Ad_compliance' => $ad_compliance, 'Hospitality' => $hospitality, 'Hosting_quality' => $hosting_quality];
    }

    public function images($id){
        $images = Villa::findorFail($id)->images;
        return new VillaImages($images);
    }

    public function dates($id){
        $dates=Villa::findOrFail($id)->dates;
        return new VillaDatesCollection($dates);
    }

    public function reservedDates($id){
        $reservedDates=ReservedDate::where([['villa_id',$id],['status',1]])->get();
        return new VillaReservedDatesCollection($reservedDates);
    }

    public function similarVillas($id){
        $villaState=Villa::findOrFail($id)->state;
        $similarVillas=Villa::where([['state',$villaState],['id','!=',$id]])->orderBy('id','desc')->take(4)->get();
        return new PopularVillasCollection($similarVillas);
    }

    
}
