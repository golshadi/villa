<?php

namespace App\Http\Resources\v1\User;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Hekmatinasser\Verta\Verta;

class UserVillaCommentsCollection extends ResourceCollection
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
                    'user_name' => $item->user->fullname,
                    'parent_id' => $item->parent_id,
                    'text'=>$item->text,
                    'created_at'=> Verta::instance($item->created_at)->format('%Y ,%B %d'),
                ];
            })
        ];     
     }
}
