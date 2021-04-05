<?php

namespace App\Http\Resources\v1\Villa;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Verta;

class VillaDatesCollection extends ResourceCollection
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
                    'date' => Verta::instance($item->date)->format('Y-n-j'),
                    'status'=>$item->status==0 ? 'خالی' : 'رزرو شده',
                    'special_price'=>$item->special_price,
                ];
            })
        ];      
    }
} 
