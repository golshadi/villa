<?php

namespace App\Http\Resources\v1\User;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Verta;

class UserTransactionsCollection extends ResourceCollection
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
                    'type' => $item->type==0 ? 'برداشت' : 'واریز',
                    'date'=>Verta::instance($item->date)->format('Y/n/j'),
                    'cost'=>$item->cost,
                    'description'=>$item->description,
                    'status'=>$item->status==0 ? 'پرداخت نشده' : 'پرداخت شده'    
                ];
            })
        ];      
    }
}
