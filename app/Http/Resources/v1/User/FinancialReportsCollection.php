<?php

namespace App\Http\Resources\v1\user;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FinancialReportsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    private $totalIncome;

    public function __construct($resource, $totalIncome)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->totalIncome = $totalIncome;
    }

    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($item) {
                return [
                    'id' => $item->id,
                    'date' => $item->date,
                    'src' => $item->src,
                    'description' => $item->description,
                    'amount' => $item->amount
                ];
            }), 
                'income' => [
                    'trappIncome' => (int) $this->totalIncome[0],
                    'otherIncome' => (int) $this->totalIncome[1],
                    'totalIncome' => $this->totalIncome[0] + $this->totalIncome[1]
                ]
            
        ];
    }
}
