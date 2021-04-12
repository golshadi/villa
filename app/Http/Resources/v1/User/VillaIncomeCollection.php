<?php

namespace App\Http\Resources\v1\user;

use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Http\Resources\Json\ResourceCollection;

class VillaIncomeCollection extends ResourceCollection
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
                    'guest' => $item->user->fullname,
                    'passengers_number'=>$item->passengers_number,
                    'final_cost'=>$item->final_cost,
                    'start_date'=>Verta::instance($item->start_date)->format('Y/n/j'),
                    'end_date'=>Verta::instance($item->end_date)->format('Y/n/j'),
                ];
            })
        ]; 
    }
}
