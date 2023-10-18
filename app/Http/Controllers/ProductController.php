<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductDetailsResource;
use App\Http\Resources\ProductListResource;
use App\Models\ModeratorType;
use App\Models\Package;
use App\Models\Product;
use App\Models\Version;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //

    public function index($type=null)
    {

        $author_type = ModeratorType::where('is_author',1)->pluck('id');
        // $version_type=array_search($type, Version::getTypes());

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
                ,'productable:id'
                ,'productable' => function (MorphTo $morphTo) {
                    $morphTo->constrain([
                        Version::class => function ($query) {
                            $query
                            ->select('id','type','alert_quantity','active')
                            ->with([
                                'moderators:id,author_id,moderator_type,version_id',
                                // 'moderators.moderators_type:id,name',
                                'moderators.author:id,name',

                            ]);
                        },

                    ]);
                },

            ])
        ->active()
        ->when($type == 'books',function($q){
            $q->whereHas('book_only');
        })
        ->when($type == 'lecture-sheets',function($q){
            $q->whereHas('lecture_sheet_only');
        })
        ->orderBy('id','desc')
        // ->count();
        ->paginate();
        // $products;
        // response(ProductListResource::collection($products));
        // return
        return response()->json([
            'data' => ProductListResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
            'links' => [
                'prev' => $products->previousPageUrl(),
                'next' => $products->nextPageUrl(),
            ],
        ]);
    }

    function show(Product $product) {
        $product->load([
            'prices'=>function($q) {
                $q->whereIn('price_category_id',[4,9]);
            }
        ]);
        $productResource = new ProductDetailsResource($product);
        return
        response($productResource,200);

    }


}
