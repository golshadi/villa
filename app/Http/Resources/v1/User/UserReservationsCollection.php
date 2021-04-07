<?php

namespace App\Http\Resources\v1\User;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Verta;

class UserReservationsCollection extends ResourceCollection
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
                    'villa_title' => $item->villa_title,
                    'state'=>$item->state,
                    'city'=>$item->city,
                    'entry_date'=>Verta::instance($item->entry_date)->format('Y/n/j'),
                    'exit_date'=>Verta::instance($item->exit_date)->format('Y/n/j'),
                    'cost'=>$item->cost,
                    'pay_status'=> $item->pay_status, 
                    // 0 => در انتظار پذیرش میزبان ,
                    // 1 => در انتظار پرداخت ,
                    // 2 => پرداخت شد
                    'villa_id'=>$item->villa_id
                ];
            })
        ];          
    }
}
