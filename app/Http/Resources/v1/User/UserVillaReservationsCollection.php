<?php

namespace App\Http\Resources\v1\User;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Verta;

class UserVillaReservationsCollection extends ResourceCollection
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
                    'title' => $item->user->fullname,
                    'guest_name'=>$item->id,
                    'passengers_number'=>$item->passengers_number,
                    'start_date'=>Verta::instance($item->start_date)->format('Y/n/j'),
                    'end_date'=>Verta::instance($item->end_date)->format('Y/n/j'),
                    'satus'=>$item->status // 1=Confirmed | 0=UnConfirmed
                ];
            })
        ];
    }
}
