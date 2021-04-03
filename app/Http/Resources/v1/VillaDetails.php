<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class VillaDetails extends JsonResource
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
            'standard_capacity'=>$this->standard_capacity,
            'max_capacity'=>$this->max_capacity,
            'rent_type'=>$this->rent_type,
            'bedroom'=>$this->bedroom,
            'ir_toilet'=>$this->ir_toilet,
            'eu_toilet'=>$this->eu_toilet,
            'shower'=>$this->shower,
            'shared_bathroom'=>$this->shared_bathroom == 0 ? false : true,
            'places'=>explode(',',$this->places),
            'view'=>$this->view,
            'area'=>$this->area
        ];
    }
}
