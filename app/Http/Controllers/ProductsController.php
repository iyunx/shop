<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
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
        
        //搜索排序集合
        $filters = ['search'=>$search, 'order'=>$order];

        return view('product.index', compact('products', 'filters'));
    }

    public function show(Product $product)
    {
        if (!$product->on_sale) {
            // InvalidRequestException自定义的错误类，app\exceptions
            // make:exception 新建错误类
            throw new InvalidRequestException('商品未上架');
        }
        return view('product.show', compact('product'));
    }
}
