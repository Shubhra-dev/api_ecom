<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            "id" => $this->id,
            "name" => $this->product_name->name,
            "image"=> $this->img,
            "category"=> $this->categories->pluck('name') ?? '',
            "isbn"=> $this->productable->volumes->pluck('volumes.isbn')->implode(', ')  ?? '',
            // 'type'              => (string) ($this->productable_type == Package::class ? 'Package' : 'Single'),
            "author"=> $this->productable->moderators ? $this->productable->moderators->pluck('author.name'): '',
            // "author"=> $this->productable->moderators ? $this->productable->moderators->pluck('author.name')->implode(', ') : '',
            "newPrice"=> ($this->prices->where('price_category_id',9)->first()->amount??$this->prices->where('price_category_id',4)->first()->amount) ?? 0,
            "oldPrice"=> $this->prices->count() > 1 ? $this->prices->where('price_category_id',4)->first()->amount : 0,
            "publisher"=> $this->productable->production->publisher->name,
            // "publish_date"=>  $this->productable->release_date ,
            "publish_date"=>  $this->productable->release_date ? date("F jS Y", strtotime($this->productable->release_date)) : '',
            // "publish_date"=> date('Y-m-d', $this->productable->release_date),
            // "rating"=> 4.2,
        ];
    }
}
