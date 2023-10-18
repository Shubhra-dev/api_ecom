<?php

namespace App\Traits;

use App\Models\Package;
use App\Models\Version;
use App\Models\Volume;


trait ProductSearch
{
    static $scope_search = false; 

    
    public function scopeSearch($query, array $columns, array $relations = [], $operator = 'regexp')
    {
        self::$scope_search = true;
        $search = preg_replace('/ /', '%', request()->search);
        // return
        $search = request()->search ? explode(',', $search) : NULL;
        $search_by_name = $search[0] ?? '';

        return $query
            ->when(isset(request()->search), function ($query) use ($columns, $relations, $operator, $search_by_name) {
                $query->where(function ($query) use ($columns, $relations, $operator, $search_by_name) {
                    foreach ($columns as $column) {
                        if ($column) {
                            $query->orWhere($column, $operator, $search_by_name);
                        }
                    }

                    // abid.himu:name,gender
                    // abid.himu [0], name,gender [1]
                    foreach ($relations as $relation) {
                        if ($relation) {
                            $query->orWhereHas(explode(':', $relation)[0], function ($query) use ($relation, $operator, $search_by_name) {
                                foreach (explode(',', explode(':', $relation)[1] ?? '') as $relation_column) {
                                    $query->where(function ($query) use ($relation_column, $operator, $search_by_name) {
                                        if ($relation_column) {
                                            $query->orWhere($relation_column, $operator, $search_by_name);
                                        }
                                    });
                                }
                            });
                        }
                    }
                });
            });
    }

    public function scopeProductSearch($query, $req_search, $relation = null)
    {
        
        
        if($relation){
            if(self::$scope_search){
                return
                $query->orWhere(function($query) use($req_search,$relation){
                    return $this->scopeProductSearchWithRelation($query, $req_search, $relation);
                });
            }else{
                return
                $query->where(function($query) use($req_search,$relation){
                    return $this->scopeProductSearchWithRelation($query, $req_search, $relation);
                });
            }
        }else{
            if(self::$scope_search){
                return
                $query->orWhere(function($query) use($req_search){
                    return $this->scopeProductSearchWithoutRelation($query, $req_search);
                });
            }else{
                return
                $query->where(function($query) use($req_search){
                    return $this->scopeProductSearchWithoutRelation($query, $req_search);
                });
            }
        }
        
    }
    
    public function scopeProductSearchWithoutRelation($query, $req_search)
    {

        // $relation = 'product';
        $search = preg_replace('/ /', '%', $req_search);
        // return
        $search = $req_search ? explode(',', $search) : NULL;
        $search_by_name = $search[0] ?? '';
        $search_by_edition = $search[1] ?? '';
        $search_by_vol = $search[2] ?? '';

        return $query

            ->when($req_search, function ($query) use ($search_by_name, $search_by_edition, $search_by_vol) {
                $query
                    // ->WhereHas($relation, function ($query) use ($search_by_name, $search_by_edition, $search_by_vol) {
                        // $query
                            ->where(function ($query) use ($search_by_name) {
                                $query
                                    ->WhereHasMorph('productable', Package::class, function ($query) use ($search_by_name) {
                                        $query->where('name', 'like', "%{$search_by_name}%")
                                            ->orWhereHas('package_products.product', function ($query) use ($search_by_name) {
                                                $query->WhereHasMorph('productable', Version::class, function ($query) use ($search_by_name) {
                                                    $query->whereHas('production', function ($query) use ($search_by_name) {
                                                        $query->where('name', 'like', "%{$search_by_name}%");
                                                    });
                                                })->orWhereHasMorph('productable', Volume::class, function ($query) use ($search_by_name) {
                                                    $query->WhereHas('version.production', function ($query) use ($search_by_name) {
                                                        $query->where('name', 'like', "%{$search_by_name}%");
                                                    });
                                                });
                                            });
                                    })
                                    ->orWhereHasMorph('productable', Version::class, function ($query) use ($search_by_name) {
                                        $query
                                            ->whereHas('production', function ($query) use ($search_by_name) {
                                                $query->where('name', 'like', "%{$search_by_name}%");
                                            });
                                    })
                                    ->orWhereHasMorph('productable', Volume::class, function ($query) use ($search_by_name) {
                                        $query->WhereHas('version.production', function ($query) use ($search_by_name) {
                                            $query->where('name', 'like', "%{$search_by_name}%");
                                        });
                                    });
                            })
                            ->when($search_by_edition != '', function ($query) use ($search_by_edition) {
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
                            })
                            ->when($search_by_vol != '', function ($query) use ($search_by_vol) {

                                $query->where(function ($query) use ($search_by_vol) {
                                    $query->whereHasMorph('productable', Version::class, function ($query) use ($search_by_vol) {
                                        $query
                                            ->whereHas('volumes', function ($query) use ($search_by_vol) {
                                                $query->where('name', 'like', "%{$search_by_vol}%");
                                            });
                                    })
                                        ->orWhereHasMorph('productable', Volume::class, function ($query) use ($search_by_vol) {
                                            $query
                                                ->Where('name', 'regexp',   $search_by_vol);
                                        });
                                });
                            })
                            ;
                    // });
                // $this->generalSearch($query,['name'],['outlet']);
                // ->orWhereHas('outlet', function ($query) use ($search_by_name) {
                //     $query
                //         ->Where('name', 'regexp',   $search_by_name);
                });
            // });
    }
    public function scopeProductSearchWithRelation($query, $req_search, $relation)
    {


        // $relation = 'product';
        // $relation = null;
        $search = preg_replace('/ /', '%', $req_search);
        // return
        $search = $req_search ? explode(',', $search) : NULL;
        $search_by_name = $search[0] ?? '';
        $search_by_edition = $search[1] ?? '';
        $search_by_vol = $search[2] ?? '';

        return $query

            ->when($req_search, function ($query) use ($search_by_name, $search_by_edition, $search_by_vol, $relation) {
                $query
                    ->WhereHas($relation, function ($query) use ($search_by_name, $search_by_edition, $search_by_vol) {
                        $query
                            ->where(function ($query) use ($search_by_name) {
                                $query
                                    ->WhereHasMorph('productable', Package::class, function ($query) use ($search_by_name) {
                                        $query->where('name', 'like', "%{$search_by_name}%")
                                            ->orWhereHas('package_products.product', function ($query) use ($search_by_name) {
                                                $query->WhereHasMorph('productable', Version::class, function ($query) use ($search_by_name) {
                                                    $query->whereHas('production', function ($query) use ($search_by_name) {
                                                        $query->where('name', 'like', "%{$search_by_name}%");
                                                    });
                                                })->orWhereHasMorph('productable', Volume::class, function ($query) use ($search_by_name) {
                                                    $query->WhereHas('version.production', function ($query) use ($search_by_name) {
                                                        $query->where('name', 'like', "%{$search_by_name}%");
                                                    });
                                                });
                                            });
                                    })
                                    ->orWhereHasMorph('productable', Version::class, function ($query) use ($search_by_name) {
                                        $query
                                            ->whereHas('production', function ($query) use ($search_by_name) {
                                                $query->where('name', 'like', "%{$search_by_name}%");
                                            });
                                    })
                                    ->orWhereHasMorph('productable', Volume::class, function ($query) use ($search_by_name) {
                                        $query->WhereHas('version.production', function ($query) use ($search_by_name) {
                                            $query->where('name', 'like', "%{$search_by_name}%");
                                        });
                                    });
                            })
                            ->when($search_by_edition != '', function ($query) use ($search_by_edition) {
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
                            })
                            ->when($search_by_vol != '', function ($query) use ($search_by_vol) {

                                $query->where(function ($query) use ($search_by_vol) {
                                    $query->whereHasMorph('productable', Version::class, function ($query) use ($search_by_vol) {
                                        $query
                                            ->whereHas('volumes', function ($query) use ($search_by_vol) {
                                                $query->where('name', 'like', "%{$search_by_vol}%");
                                            });
                                    })
                                        ->orWhereHasMorph('productable', Volume::class, function ($query) use ($search_by_vol) {
                                            $query
                                                ->Where('name', 'regexp',   $search_by_vol);
                                        });
                                });
                            });
                    });
                // $this->generalSearch($query,['name'],['outlet']);
                // ->orWhereHas('outlet', function ($query) use ($search_by_name) {
                //     $query
                //         ->Where('name', 'regexp',   $search_by_name);
                // });
            });
    }
        // public function scopeOrProductSearch($query, $req_search)
        // {

        //     // $relation = 'product';
        //     $relation = null;

        //     $search = preg_replace('/ /', '%', $req_search);
        //     // return
        //     $search = $req_search ? explode(',', $search) : NULL;
        //     $search_by_name = $search[0] ?? '';
        //     $search_by_edition = $search[1] ?? '';
        //     $search_by_vol = $search[2] ?? '';

        //     return $query

        //         ->when($req_search, function ($query) use ($search_by_name, $search_by_edition, $search_by_vol, $relation) {
        //             $query
        //                 ->OrWhereHas($relation, function ($query) use ($search_by_name, $search_by_edition, $search_by_vol) {
        //                     $query
        //                         ->where(function ($query) use ($search_by_name) {
        //                             $query
        //                                 ->WhereHasMorph('productable', Package::class, function ($query) use ($search_by_name) {
        //                                     $query->where('name', 'like', "%{$search_by_name}%")
        //                                         ->orWhereHas('package_products.product', function ($query) use ($search_by_name) {
        //                                             $query->WhereHasMorph('productable', Version::class, function ($query) use ($search_by_name) {
        //                                                 $query->whereHas('production', function ($query) use ($search_by_name) {
        //                                                     $query->where('name', 'like', "%{$search_by_name}%");
        //                                                 });
        //                                             })->orWhereHasMorph('productable', Volume::class, function ($query) use ($search_by_name) {
        //                                                 $query->WhereHas('version.production', function ($query) use ($search_by_name) {
        //                                                     $query->where('name', 'like', "%{$search_by_name}%");
        //                                                 });
        //                                             });
        //                                         });
        //                                 })
        //                                 ->orWhereHasMorph('productable', Version::class, function ($query) use ($search_by_name) {
        //                                     $query
        //                                         ->whereHas('production', function ($query) use ($search_by_name) {
        //                                             $query->where('name', 'like', "%{$search_by_name}%");
        //                                         });
        //                                 })
        //                                 ->orWhereHasMorph('productable', Volume::class, function ($query) use ($search_by_name) {
        //                                     $query->WhereHas('version.production', function ($query) use ($search_by_name) {
        //                                         $query->where('name', 'like', "%{$search_by_name}%");
        //                                     });
        //                                 });
        //                         })
        //                         ->when($search_by_edition != '', function ($query) use ($search_by_edition) {
        //                             $query->where(function ($query) use ($search_by_edition) {
        //                                 $query->whereHasMorph('productable', Version::class, function ($query) use ($search_by_edition) {
        //                                     $query
        //                                         ->Where('edition', 'regexp',   $search_by_edition);
        //                                 })
        //                                     ->orWhereHasMorph('productable', Volume::class, function ($query) use ($search_by_edition) {
        //                                         $query
        //                                             ->WhereHas('version', function ($query) use ($search_by_edition) {
        //                                                 $query->where('edition', 'regexp',   $search_by_edition);
        //                                             });
        //                                     });
        //                             });
        //                         })
        //                         ->when($search_by_vol != '', function ($query) use ($search_by_vol) {

        //                             $query->where(function ($query) use ($search_by_vol) {
        //                                 $query->whereHasMorph('productable', Version::class, function ($query) use ($search_by_vol) {
        //                                     $query
        //                                         ->whereHas('volumes', function ($query) use ($search_by_vol) {
        //                                             $query->where('name', 'like', "%{$search_by_vol}%");
        //                                         });
        //                                 })
        //                                     ->orWhereHasMorph('productable', Volume::class, function ($query) use ($search_by_vol) {
        //                                         $query
        //                                             ->Where('name', 'regexp',   $search_by_vol);
        //                                     });
        //                             });
        //                         });
        //                 });
        //             // $this->generalSearch($query,['name'],['outlet']);
        //             // ->orWhereHas('outlet', function ($query) use ($search_by_name) {
        //             //     $query
        //             //         ->Where('name', 'regexp',   $search_by_name);
        //             // });
        //         });
        // }
}
