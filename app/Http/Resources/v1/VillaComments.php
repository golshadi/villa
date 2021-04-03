<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Verta;

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
        // Ensure you call the parent constructor
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
                ];
            }),
            'scores'=>[
                $this->scores
            ]
        ];  
    }
}
