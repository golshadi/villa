<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DiscountedVillasCollection extends ResourceCollection
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
                    'villa' => $item->villa->only('title', 'state', 'city', 'main_img', 'score'),
                    'normal_cost' => $item->normal_cost,
                    'weekly_discount' => $item->weekly_discount,
                    'monthly_discount' => $item->monthly_discount,
                    'normal_cost' => $item->normal_cost,
                    'details' => $item->villa->detail ? $item->villa->detail->only('bedroom', 'max_capacity') : null,
                ];

            })
        ];
    }
}
