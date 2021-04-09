<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Other\PopularVillasCollection;
use App\Models\Favorite;
use App\Models\Villa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    
    public function getFavorites(){
        $data=[];
        $user=Auth::user();
        $favorites= $user->favorites;
        foreach($favorites as $key=>$value){
            $data[$key]=$value->villa_id;
        }
        $favorites=Villa::whereIn('id',$data)->get();
        return new PopularVillasCollection($favorites);
    }

    
    public function addToFavorite(Request $request)
    {
        $user=Auth::user();
        $villa_id=$request->villa_id;
        $user->favorites()->create(['villa_id'=>$villa_id]);
        return response()->json(['result' => 'Favorite added']);
    }

    public function removeFromFavorite(Request $request)
    {
        $user=Auth::user();
        $villa_id=$request->villa_id;
        Favorite::where([['villa_id',$villa_id],['user_id',$user->id]])->delete();
        return response()->json(['result' => 'Favorite removed']);
    }
    
}
