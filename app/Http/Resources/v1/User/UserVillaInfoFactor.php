<?php

namespace App\Http\Resources\v1\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserVillaInfoFactor extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    private $details;
    
    public function __construct($resource, $details)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->details = $details;
    }
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'state'=>$this->state,
            'city'=>$this->city,
            'score'=>$this->score,
            'main_img'=>$this->main_img,
            'rent_type'=>$this->rent_type,
            'standard_capacity'=>$this->details->standard_capacity,
            'max_capacity'=>$this->details->max_capacity,
            'bedroom'=>$this->details->bedroom,
            'shower'=>$this->details->shower,
            'ir_toilet'=>$this->details->ir_toilet,
            'eu_toilet'=>$this->details->eu_toilet,
            'places'=>explode(',',$this->details->places),
        ];
    }
}
