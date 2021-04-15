<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\User\UserVillaCommentsCollection;
use App\Http\Resources\v1\Villa\VillaComments;
use App\Models\Comment;
use App\Models\Search;
use App\Models\Villa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    
    public function getVillaComments($id)
    {
        $comments = Villa::findorFail($id)->comments;
        $comments_count = Comment::where([['villa_id', $id], ['parent_id', 0]])->count();
        $scores = $this->calcaulateScores($comments, $comments_count);
        return new VillaComments($comments, $scores);
    }

    public function getUserVillaComments($id){
        $user=Auth::user();
        $villa=Villa::where([['id',$id],['user_id',$user->id]])->first();
        if($villa){
            $comments=$villa->comments;
            return new UserVillaCommentsCollection($comments);
        }
        return response()->json(['status'=>0,'data'=>'Something went wrong!']);
    }

    public function replayComment(Request $request,$villaId,$parentId){

        $this->validate($request, [
            'text' => 'required'
        ]);
        
        Comment::create([
            'villa_id'=>$villaId,
            'user_id'=>Auth::user()->id,
            'parent_id'=>$parentId,
            'text'=>$request->text,
        ]);

        return response()->json(['data'=>'The comment was answered']);
    }

    public function addComment(Request $request,$villaId){

        $validatedData=$this->validate($request, [
            'text' => 'required',
            'total_score'=>'required|numeric|min:0|max:5',
            'cleaning'=>'required|numeric|min:0|max:5',
            'ad_compliance'=>'required|numeric|min:0|max:5',
            'hospitality'=>'required|numeric|min:0|max:5',
            'hosting_quality'=>'required|numeric|min:0|max:5'
        ]);

        $user=Auth::user();
        $villa=Villa::findOrFail($villaId);
        $villa->comments()->create([
            'user_id'=>$user->id,
            'text'=>$request->text,
            'total_score'=>$request->total_score,
            'cleaning'=>$request->cleaning,
            'ad_compliance'=>$request->ad_compliance,
            'hospitality'=>$request->hospitality,
            'hosting_quality'=>$request->hosting_quality 
        ]);

        $final_score=$this->calculateCommentScore($villa,$request->total_score);

        $villa->update([
            'score'=>$final_score
        ]);
        Search::where('villa_id',$villa->id)->update([
            'score'=>$final_score
        ]);
        return response()->json(['data'=>'Comment added']);
    }

    public function calculateCommentScore($villa,$user_score){

        $comments_count=$villa->comments()->where('parent_id',0)->count();
        $score_sum=$villa->comments()->sum('total_score');
        $final_score=round(($score_sum+$user_score)/($comments_count+1),1);
        return $final_score;
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

        $cleaning = round($cleaning / $comments_count,1);
        $ad_compliance = round($ad_compliance / $comments_count,1);
        $hospitality = round($hospitality / $comments_count,1);
        $hosting_quality = round($hosting_quality / $comments_count,1);

        return ['Cleaning' => $cleaning, 'Ad_compliance' => $ad_compliance, 'Hospitality' => $hospitality, 'Hosting_quality' => $hosting_quality];
    }
}