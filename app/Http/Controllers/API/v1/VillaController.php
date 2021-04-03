<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Villa;
use Illuminate\Http\Request;
use App\Http\Resources\v1\Villa as VillaResource;
use App\Http\Resources\v1\VillaComments;
use App\Http\Resources\v1\VillaImages;
use App\Models\Comment;

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

    
}
