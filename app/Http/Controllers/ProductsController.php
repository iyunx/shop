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

    public function show(Product $product, Request $request)
    {
        if (!$product->on_sale) {
            // InvalidRequestException自定义的错误类，app\exceptions
            // make:exception 新建错误类
            throw new InvalidRequestException('商品未上架');
        }

        $favored = false;
        if($user = $request->user()){
            //boolval() 找到返回true, 否则false
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }
        return view('product.show', compact('product', 'favored'));
    }

    //商品收藏
    public function favor(Product $product, Request $request)
    {
        $user = $request->user();
        //如果此商品已经被收藏
        if ($user->favoriteProducts()->find($product->id)) return [];

        //通过 attach() 方法将当前用户和此商品关联起来。或：attach($product->id)
        $user->favoriteProducts()->attach($product);
        
        return [];
    }

    //取消收藏
    public function disfavor(Product $product, Request $request)
    {
        $user = $request->user();
        //detach() 方法用于取消多对多的关联，接受的参数个数与 attach() 方法一致。
        $user->favoriteProducts()->detach($product);
        return [];
    }

    //用户商品收藏，页面
    public function favorites(Request $request)
    {
        $products = $request->user()->favoriteProducts()->paginate();
        return view('product.favorites', ['products'=>$products]);
    }
}
