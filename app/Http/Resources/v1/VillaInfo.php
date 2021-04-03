<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class VillaInfo extends JsonResource
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
            'general_fac'=>explode(',',$this->general_fac),
            'kitchen_fac'=>explode(',',$this->kitchen_fac),
            'temp_fac'=>explode(',',$this->temp_fac),
            'chef'=>$this->chef,
            'host'=>$this->host,
            'tour_guide'=>$this->tour_guide,
            'bodyguard'=>$this->bodyguard,
            'catering'=>explode(',',$this->catering)
        ];
    }
}
