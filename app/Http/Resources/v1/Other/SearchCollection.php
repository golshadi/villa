<?php

namespace App\Http\Resources\v1\Other;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SearchCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($item) {
                return [
                    'id'=>$item->id,
                    'title'=>$item->title,
                    'state' => $item->state,
                    'city' => $item->city,
                    'village' => $item->village,
                    'score' => $item->score,
                    'main_img'=>$item->main_img,
                    'details' => $item['detail'],
                    'rules' => $item['rile']
                ];
            })
        ]; 
    }
}
