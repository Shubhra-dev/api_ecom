<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    static $EditPassword = false;

    public function toArray($request)
    {

        $data= [];
        $data =  [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email ,
            // 'gender' => $this->gender,
            // 'bmdc' => $this->bmdc,
            // 'medical' => $this->medical,
            // 'designation' => $this->designation,
        ];

        if(self::$EditPassword){
            $data ['password'] = $this->password;
        }
        return $data;
    }
}
