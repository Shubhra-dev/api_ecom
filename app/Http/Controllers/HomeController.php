<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductListResource;
use App\Models\HomeProduct;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //

    public function trending_products()
    {

        $trends = HomeProduct::query()
        ->where('type',1)
        ->pluck('product_id');

        return
        $this->product_list( $trends);
    }
    public function best_sale()
    {

        $best_sale = HomeProduct::query()
        ->where('type',2)
        ->pluck('product_id');

        return
        $this->product_list( $best_sale);
    }
    public function top_rated()
    {

        $top_rated = HomeProduct::query()
        ->where('type',3)
        ->pluck('product_id');

        return
        $this->product_list( $top_rated);
    }
    public function may_like()
    {

        $may_like = HomeProduct::query()
        ->where('type',4)
        ->pluck('product_id');

        return
        $this->product_list( $may_like);
    }

    public function product_list($filter=null)
    {
        // return
        $products =  Product::query()
        ->with([
                'categories'=> function($query) {
                    $query->take(1);
                }
                , 'product_name'
                , 'prices' => function($query) {
                    $query->where('price_category_id', 4);
                }
                ,'productable'
            ])
        ->when($filter!=null, function($q) use($filter){
            $q->whereIn('id',$filter);
        })
        ->active()
        // ->take(15)
        ->get();
        return
        response(ProductListResource::collection($products));
    }
}
