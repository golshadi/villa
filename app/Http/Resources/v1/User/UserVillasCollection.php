<?php

namespace App\Http\Resources\v1\User;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserVillasCollection extends ResourceCollection
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
                    'title' => $item->title,
                    'main_img'=>$item->main_img,
                    'status'=>($item->status==0) ? 'در انتظار تایید' : 
                    (($item->status==1)? 'غیر فعال' : 'تایید شد')
                ];
            })
        ];      
    }
}
