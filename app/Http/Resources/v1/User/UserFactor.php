<?php

namespace App\Http\Resources\v1\User;

use Illuminate\Http\Resources\Json\JsonResource;
use Hekmatinasser\Verta\Verta;

class UserFactor extends JsonResource
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
            'id' => $this->id,
            'issue_date' => Verta::instance($this->created_at)->format('Y/n/j'),
            'pay_status' => $this->pay_status,
            'entry_date' => Verta::instance($this->entry_date)->format('Y/n/j'),
            'exit_date' => Verta::instance($this->exit_date)->format('Y/n/j'),
            'length_stay' => $this->length_stay,
            'extra_people' => $this->extra_people,
            'cost' => $this->cost,
            'villa' => new UserVillaInfoFactor($this->villa,$this->details)
        ];
    }
}
