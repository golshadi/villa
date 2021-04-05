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
                    'title' => $item->title,
                    'state' => $item->state,
                    'city' => $item->city,
                    'main_img' => $item->main_img,
                    'score' => $item->score,
                    'details' => $item->detail ? $item->detail->only('bedroom', 'max_capacity') : null,
                    'rules' => $item->rule ? $item->rule->only('normal_cost') : null
                ];
            })
        ];
    }
}
