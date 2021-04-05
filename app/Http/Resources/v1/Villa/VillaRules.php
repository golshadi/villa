<?php

namespace App\Http\Resources\v1\Villa;

use Illuminate\Http\Resources\Json\JsonResource;

class VillaRules extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'normal_cost'=>$this->normal_cost,
            'special_cost'=>$this->special_cost,
            'normal_extra_cost'=>$this->normal_extra_cost,
            'special_extra_cost'=>$this->special_extra_cost,
            'weekly_discount'=>$this->weekly_discount,
            'monthly_discount'=>$this->monthly_discount,
            'auth_rules'=>explode(',',$this->auth_rules),
            'special_rules'=>$this->special_rules,
            'min_reserve'=>$this->min_reserve,
            'max_reserve'=>$this->max_reserve,
            'suitable_for'=>explode(',',$this->suitable_for),
            'arrival_time'=>$this->arrival_tima,
            'exit_time'=>$this->exit_time
        ];
    }
}
