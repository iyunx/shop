<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $builder = Product::query()->where('on_sale', true);

        //搜索词
        if($search = $request->search){
            $like = '%'.$search.'%';
            $builder->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function($query) use ($like){
                        $query->where('title', 'like', $like)
                              ->orWhere('description', 'like', $like);
                    });
        }

        //order排序
        if($order = $request->order){
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                if(in_array($m[1], ['price', 'sold_count', 'rating'])){
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }
        $products = $builder->paginate(16);
        $filters = ['search'=>$search, 'order'=>$order];
        return view('product.index', compact('products', 'filters'));
    }
}
