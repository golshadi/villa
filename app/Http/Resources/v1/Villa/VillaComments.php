<?php

namespace App\Http\Resources\v1\Villa;

use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\ResourceCollection;

class VillaComments extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    private $scores;

    public function __construct($resource, $scores)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->scores = $scores;
    }
  

    public function toArray($request)
    {
       
        return [
            'data' => $this->collection->map(function($item) {
                return [
                    'id'=>$item->id,
                    'user_name' => $item->user->fullname,
                    'parent_id' => $item->parent_id,
                    'text'=>$item->text,
                    'created_at'=> Verta::instance($item->created_at)->format('%Y ,%B %d'),
                    'answer'=>$item->answer
                ];
            }),
            'scores'=>[
                $this->scores
            ]
        ];  
    }
}
