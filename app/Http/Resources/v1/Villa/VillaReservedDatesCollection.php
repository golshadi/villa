<?php

namespace App\Http\Resources\v1\Villa;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Hekmatinasser\Verta\Verta;

class VillaReservedDatesCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    private $customizedDates;
    
    public function __construct($resource,$customizedDates)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->customizedDates = $customizedDates;
    }

    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($item) {
                return [
                    'id'=>$item->id,
                    'start_date'=>Verta::instance($item->start_date)->format('Y/n/j'),
                    'end_date'=>Verta::instance($item->end_date)->format('Y/n/j')
                ];
            }),
            'customizedDates' => $this->customizedDates->map(function($item) {
                return [
                    'date'=>Verta::instance($item->start_date)->format('Y/n/j'),
                    'status'=>$item->status, // 0 => Empty  |  1 => Reserved
                    'psecial_price'=>$item->special_price
                ];
            })
        ];
    } 
}
