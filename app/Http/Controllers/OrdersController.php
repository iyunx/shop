<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function store(OrderRequest $request)
    {
        $user = $request->user();
        // 开启一个数据库事务
        //DB::transaction() 方法会开启一个数据库事务，在回调函数里的所有 SQL 写操作都会被包含在这个事务里，
        //如果回调函数抛出异常则会自动回滚这个事务，否则提交事务。用这个方法可以帮我们节省不少代码。
        $order = DB::transaction(function() use ($user, $request){
            $address = UserAddress::find($request->address_id);
            //更新此地址的最后使用时间
            $address->update(['last_used_at', Carbon::now()]);
            // 创建order订单
            $order = new Order([
                //注意address等字段对应数据库表字段
                //address以json形式保存
                'address' => [
                    'address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark' => $request->remark,
                'total_amount' => 0,
            ]);

            //订单关联到用户 associate只适合belongsTo
            $order->user()->associate($user);
            //写入数据库
            $order->save();

            //订单商品总价
            $totalAmount = 0;

            //=====================================
            //items是OrderRequest整理结果
            $items = $request->items;

            //遍历用户提交的sku
            foreach($items as $data){
                $sku = ProductSku::find($data['sku_id']);
                //创建一个orderItem并直接与当前订单关联
                $item = $order->items()->make([
                    'amount'=>$data['amount'],
                    'price'=>$sku->price,
                ]);
                //通过OrderItem模型中的商品和sku方法，获取对应商品信息
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save(); //保存到OrderItem表

                //获取商品总价
                $totalAmount += $sku->price * $data['amount'];
                //下了订单，就对应减少商品的库存  decreaseStock()是ProductSku模型中的方法
                if($sku->decreaseStock($data['amount']) <= 0 ){
                    throw new InvalidRequestException('该商品库存不足');
                }
            }

            //更新订单总金额，保存到orders数据表
            $order->update(['total_amount'=>$totalAmount]);

            //Laravel 提供的 collect() 辅助函数快速取得所有 SKU ID，然后将本次订单中的商品 SKU 从购物车中删除。
            //就是下了订单的商品，从购物车中删除 $items是前端提交sku商品集合
            $skuId = collect($items)->pluck('sku_id');
            $user->cartItems()->whereIn('product_sku_id', $skuId)->delete();

            //完成订单，还需要减去购买的商品数量，更新库存 不能直接更新，高并发会有问题
            //ProductSku::update(['stock'=>$sku->stock - $amount]);


        });

        //返回 数据库事务集合
        return $order;
    }
}