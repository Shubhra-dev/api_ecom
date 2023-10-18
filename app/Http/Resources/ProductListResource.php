<?php

namespace App\Http\Resources;

use App\Models\Package;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {


        return [
            "id" => $this->id,
            "name" => $this->product_name->name,
            "image"=> $this->img,
            "category"=> $this->categories[0]->name ?? '',
            'type'              => (string) ($this->productable_type == Package::class ? 'Package' : 'Single'),
            "author"=> $this->productable->moderators ? $this->productable->moderators->pluck('author.name')->implode(', ') : '',
            "newPrice"=> $this->prices[0]->amount ?? 0,
            "oldPrice"=> 20.99,
            "rating"=> 4.2,
        ];
    }
}
