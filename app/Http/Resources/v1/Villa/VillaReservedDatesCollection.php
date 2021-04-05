<?php

namespace App\Http\Resources\v1\Villa;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Verta;

class VillaReservedDatesCollection extends ResourceCollection
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
                    'start_date'=>Verta::instance($item->start_date)->format('Y-n-j'),
                    'end_date'=>Verta::instance($item->end_date)->format('Y-n-j')
                ];
            })
        ];
    } 
}
