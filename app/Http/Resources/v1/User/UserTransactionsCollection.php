<?php

namespace App\Http\Resources\v1\User;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Hekmatinasser\Verta\Verta;

class UserTransactionsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    private $userId;

    public function __construct($resource,$userId)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->userId=$userId;        
    }
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($item) {
                return [
                    'id'=>$item->id,
                    'type'=>$item->user_id == $this->userId ? 'برداشت' 
                    : 'واریز',
                    'date'=>Verta::instance($item->updated_at)->format('Y/n/j'),
                    'amount' => $item->amount,
                    'description'=>$item->user_id == $this->userId ? 'برداشت پول' 
                    : 'اجاره ویلا',
                    'status'=>'پرداخت شد'  
                ];
            })
        ];      
    }
}
