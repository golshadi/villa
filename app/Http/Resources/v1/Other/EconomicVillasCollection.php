<?php

namespace App\Http\Resources\v1\Other;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EconomicVillasCollection extends ResourceCollection
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
            'data' => $this->collection->map(function ($item) {
                return [
                    'id'=>$item->id,
                    'title' => $item->villa->title,
                    'state' => $item->villa->state,
                    'city' => $item->villa->city,
                    'main_img' => $item->villa->main_img,
                    'score' => $item->villa->score,
                    'details' => $item->villa->detail ? $item->villa->detail->only('bedroom', 'max_capacity') : null,
                    'normal_cost' => $item->normal_cost
                ];
            })
        ];
    }
}
