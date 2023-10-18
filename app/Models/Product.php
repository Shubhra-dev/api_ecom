<?php

namespace App\Models;

use App\Traits\ActiveProperty;
use App\Traits\ProductSearch;
use App\Traits\ScopeDateFilter;
use App\Traits\ScopeSearch;
use App\Traits\ScopeSort;
use App\Traits\TypeProperty;
use App\Traits\WithProductRelations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes, ActiveProperty, TypeProperty, ScopeDateFilter, ScopeSort, WithProductRelations, ProductSearch;

    protected $guarded = [];

    public static $product_id = 0;
    public static $need_name = true;

    const types = [
        1 => 'Package',
        2 => 'Book',
        3 => 'Lecture',
    ];


    protected static function getTypes()
    {
        return self::types;
    }

    private function productSearchForVersion(&$query, $search_by_edition)
    {
        $query->where(function ($query) use ($search_by_edition) {
            $query->whereHasMorph('productable', Version::class, function ($query) use ($search_by_edition) {
                $query
                    ->Where('edition', 'regexp',   $search_by_edition);
            })
                ->orWhereHasMorph('productable', Volume::class, function ($query) use ($search_by_edition) {
                    $query
                        ->WhereHas('version', function ($query) use ($search_by_edition) {
                            $query->where('edition', 'regexp',   $search_by_edition);
                        });
                });
        });
    }

    private function editionSearch($query, $search_by_edition)
    {
        $query->where(function ($query) use ($search_by_edition) {
            $query->whereHasMorph('productable', Version::class, function ($query) use ($search_by_edition) {
                $query
                    ->Where('edition', 'regexp',   $search_by_edition);
            });
        });
    }

    private function volSearch($query, $search_by_vol)
    {
        $query->where(function ($query) use ($search_by_vol) {
            $query->whereHasMorph('productable', Version::class, function ($query) use ($search_by_vol) {
                $query
                    ->whereHas('volumes', function ($query) use ($search_by_vol) {
                        $query->where('name', 'like', "%{$search_by_vol}%");
                    });
            });
        });
    }

    public function scopeSearch($query, $req_search)
    {

        // $search = preg_replace('/ /', '%', $req_search);
        $search = preg_replace('/ /', '.*', $req_search);
        $search = preg_replace('/\(/', '\(', $search);
        $search = preg_replace('/\)/', '\)', $search);

         return $query->when($req_search, function ($query) use ($search) {
            $query->whereHas('product_name', function($query) use ($search) {
                $query->where("name", "regexp", "{$search}");
            })->orWhereHas('categories', function ($query) use ($search) {
                $query->Where('name', 'regexp',   $search);
            })
            ->orWhere('id', $search)
            // ->orWhereHasMorph('productable', Version::class, function ($query) use ($search) {
            //     $query->WhereHas('moderators', function ($query) use ($search) {
            //             $query->WhereHas('author', function ($query) use ($search) {
            //                 $query->where('name', 'like', "%{$search}%");
            //             });
            //         });
            // })
            ;
        });


    }

    public function scopeFilter($query)
    {
        // if(request()->type){
        //     request()->type == 1


        // }
        return $query
            ->when(request()->type, function ($query) {
                $query
                ->when(request()->type == 1, function ($query) {
                    $query->where('productable_type', Package::class);
                })
                ->when(request()->type == 2, function ($query) {
                    $query->WhereHasMorph('productable' ,  Version::class, function ($query) {
                        $query->where('type', 1 )
                        ->orWhere('type', 3 );
                    });
                })
                ->when(request()->type == 3, function ($query) {
                    $query->WhereHasMorph('productable' ,  Version::class, function ($query) {
                        $query->where('type', 2);
                    });
                });
            })
            ->when(isset(request()->active), function ($query) {
                $query->where('active', request()->active);
            })
            ->when(isset(request()->outlet), function ($query) {
                $query->WhereHas('storages', function ($query) {

                    $query->where('outlet_id', request()->outlet);
                });
                })
            ->when(isset(request()->category), function ($q) {
                $q->whereHas('categories', function ($query) {
                    $query->Where('category_id', request()->category);
                });
            })
            ->when(request()->author, function ($query) {
                $query->WhereHasMorph('productable' ,  Version::class, function ($query) {
                    $query->WhereHas('moderators', function ($query) {
                        $query->where('id', request()->author);
                    });
                });
            });
    }

    public function scopeSalePriceSecurity($query,  $product_ids)
    {
        return $query
            ->when(request()->has('memo_type') && (request()->memo_type == 0 || request()->memo_type == 1), function ($query) {
                $query
                    ->with('prices', function ($q) {
                        $q->with('price_categroy')
                            ->whereHas('price_categroy', function ($q) {
                                $q->where('type', request()->memo_type);
                            });
                    })
                    ->whereHas('prices', function ($query) {
                        $query->with('price_categroy')
                            ->whereHas('price_categroy', function ($q) {
                                $q->where('type', request()->memo_type);
                            });
                    });
            })
            ->when(request()->has('memo_type') && request()->memo_type == 2, function ($query) use ($product_ids) {
                $query->with('clients', function ($query) {
                    $query->where('customer_id', request()->customer_id)
                        ->with('price_category');
                })
                    // ->whereIn('id',array_keys($product_ids->toArray()))
                    ->with('prices', function ($q) use ($product_ids) {
                        $q
                            ->with('price_categroy.clients', function ($q) use ($product_ids) {
                                $q->where(function ($q) use ($product_ids) {
                                    foreach ($product_ids as $product_id => $price_category_id) {
                                        $q->orWhere(function ($q) use ($product_id, $price_category_id) {
                                            $q->where('product_id', $product_id)
                                                ->where('price_category_id', $price_category_id)
                                                ->where('customer_id', request()->customer_id);
                                        });
                                        // print_r([$product_id, $price_category_id,request()->customer_id]);
                                    }
                                });
                            })
                            ->whereHas('price_categroy', function ($q) use ($product_ids) {
                                $q->where(function ($q) use ($product_ids) {
                                    foreach ($product_ids as $product_id => $price_category_id) {
                                        $q->orWhere(function ($q) use ($product_id, $price_category_id) {
                                            $q->where('product_id', $product_id)
                                                ->where('price_category_id', $price_category_id);
                                        });
                                        // print_r([$product_id, $price_category_id]);
                                    }
                                });
                            })
                            ->orWhereHas('price_categroy', function ($q) {
                                $q->where('type', 0);
                            });
                    })->whereHas('prices.price_categroy', function ($q) use ($product_ids) {
                        $q->where(function ($q) use ($product_ids) {
                            foreach ($product_ids as $product_id => $price_category_id) {
                                $q->orWhere(function ($q) use ($product_id, $price_category_id) {
                                    $q->where('product_id', $product_id)
                                        ->where('price_category_id', $price_category_id);
                                });
                                // print_r([$product_id, $price_category_id]);
                            }
                        });
                    })
                    ->orWhereHas('prices.price_categroy', function ($q) {
                        $q->where('type', 0);
                    });
            });
            // ->has('storages')
            // ->whereHas('storages', function ($q) {
            //     $q->where('outlet_id', request()->outlet_id);
            // });

    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function storages()
    {
        return $this->hasMany(Storage::class);
    }

    public function category_product()
    {
        return $this->hasOne(CategoryProduct::class, 'product_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products', 'product_id', 'category_id')
            ->whereNull('category_products.deleted_at');
    }

    public function package_products()
    {
        return $this->belongsToMany(PackageProduct::class, 'package_products', 'package_id', 'product_id')->whereNull('package_products.deleted_at');
    }

    public function packages()
    {
        return $this->hasManyThrough(Package::class, PackageProduct::class, 'package_id', 'id', 'id', 'product_id' );
    }

    public function price_categories()
    {
        return $this->belongsToMany(PriceCategory::class, 'pricings', 'product_id', 'price_category_id');
    }

    public function prices()
    {
        return $this->hasMany(Pricing::class, 'product_id', 'id');
    }

    public function productable()
    {
        return $this->morphTo();
    }

    public function client_customer()
    {
        return $this->belongsToMany(Customer::class, 'client');
    }
    public function clients()
    {
        return $this->hasMany(Client::class);
    }
    public function sale_details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    /**
     * Get all of the missings for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function missings(): HasMany
    {
        return $this->hasMany(SunkCirculation::class);
    }

    public function product_name()
    {
        return $this->hasOne(ProductName::class);
    }
    public function product_return()
    {
        return $this->hasMany(ReturnProduct::class);
    }

    /**
     * Get all of the comments for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function product_otw()
    {
        return $this->hasOne(ProductOtw::class);
    }


    public function book_only()
    {
        return $this->hasOne(ProductBook::class,'id','id');
    }
    public function lecture_sheet_only()
    {
        return $this->hasOne(ProductLectureSheet::class,'id','id');
    }




    // function author()  {
    //     return $this->productable()->;
    // }

    /**
     * Get all of the comments for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function moderators(): HasManyThrough
    {
        return $this->hasManyThrough(Moderator::class, Version::class,'id','version_id','productable_id','id');
    }
}
