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
                    'id'=>$item->villa->id,
                    'title'=>$item->villa->title,
                    'type' => $item->villa->type,
                    
                ];
            })
        ]; 
    }
}
