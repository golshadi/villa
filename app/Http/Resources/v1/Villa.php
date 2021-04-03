<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class Villa extends JsonResource
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
            'id'=>$this->id,
            'title'=>$this->title,
            'type'=>$this->type,
            'phone_number'=>$this->phone_number,
            'story'=>$this->story,
            'state'=>$this->state,
            'city'=>$this->city,
            'village'=>$this->village,
            'postal_code'=>$this->postal_code,
            'address'=>$this->address,
            'long'=>$this->long,
            'lat'=>$this->lat,
            'user_name'=>$this->user->fullname,
            'score'=>$this->score,
            'details'=>new VillaDetails($this->detail),
            'rules'=>new VillaRules($this->rule),
            'info'=>new VillaInfo($this->info)
        ];
    }
}
