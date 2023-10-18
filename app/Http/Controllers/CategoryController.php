<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    function index() {
        $category_parents = Category::query()
        // ->withCount('products')
        ->where('parent_id',0)
        ->get(['id','name'])->loadCount('products');
        // ->get();
        return response($category_parents,200);
    }
}
