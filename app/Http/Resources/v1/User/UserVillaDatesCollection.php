<?php

namespace App\Http\Resources\v1\User;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Hekmatinasser\Verta\Verta;

class UserVillaDatesCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    private $rules;
    private $reserves;
    
    public function __construct($resource, $rules,$reserves)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->rules = $rules;
        $this->reserves = $reserves;
    }
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($item) {
                return [
                    'id'=>$item->id,
                    'date' => Verta::instance($item->date)->format('Y/n/j'),
                    'status'=>$item->status,
                    'special_price'=>$item->special_price,
                ];
            }),
            'reserves' => $this->reserves->map(function($item) {
                return [
                    'start_date'=>Verta::instance($item->start_date)->format('Y/n/j'),
                    'end_date' => Verta::instance($item->end_date)->format('Y/n/j'),
                ];
            }),
            'rules'=>[
                'villa_id'=>$this->rules->villa_id,
                'normal_cost'=>$this->rules->normal_cost,
                'special_cost'=>$this->rules->special_cost,
            ]
        ];    
    }
}
