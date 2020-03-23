<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Models\CartItem;
use App\Models\ProductSku;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // 购物车首页
    public function index(Request $request)
    {
        //可以使用 「点」 语法预加载嵌套关联。
        $cartItems = $request->user()->cartItems()->with(['productSku.product'])->get();
        $addresses = $request->user()->address()->orderByDesc('last_used_at')->get();
        return view('cart.index', compact('cartItems', 'addresses'));
    }

    public function add(CartRequest $request)
    {
        $user = $request->user();
        $skuId = $request->sku_id;
        $amount = $request->amount; //购买数量
        // 从数据库中查询该商品是否已经在购物车中
        if($cart = $user->cartItems()->where('product_sku_id', $skuId)->first()){
            // 如果存在则直接叠加商品数量
            $cart->update(['amount'=> $cart->amount + $amount]);
        }else{
            //创建一个新的购物车记录
            $cart = new CartItem(['amount'=> $amount]);
            //associate() 更新「从属」关联，当更新⼀个 belongsTo 关联时，可以使⽤ associate ⽅法
            $cart->user()->associate($user);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }

        return;
    }

    public function remove(ProductSku $sku, Request $request)
    {
        $request->user()->cartItems()->where('product_sku_id', $sku->id)->delete();
        return;
    }

}
