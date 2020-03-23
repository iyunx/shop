@extends('layouts.app')
@section('title', '订单中心')
@section('content')
<div class="row">
    <div class="col-lg-10 offset-1">
        <div class="card">
            <div class="card-header">订单中心</div>
            <div class="card-body">
                @foreach ($orders as $order)
                <div class="card @if(!$loop->first)mt-3 @endif">
                    <div class="card-header">
                        订单号：{{$order->no}}
                        {{-- <span class="float-right">创建时间：{{$order->created_at->diffForHumans()}}</span> --}}
                        <span class="float-right">创建时间：{{$order->created_at->format('Y-m-d H:i:s')}}</span>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>商品信息</th>
                                    <th class="text-center">单价</th>
                                    <th class="text-center">数量</th>
                                    <th class="text-center">订单总价</th>
                                    <th class="text-center">状态</th>
                                    <th class="text-center">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $index=>$item)
                                <tr class="product-info">
                                    <td scope="row">
                                        <div class="media">
                                            <a class="d-flex mr-2" href="{{route('products.show', $item->product->id)}}">
                                                <img src="{{$item->product->image}}" alt="" width="90">
                                            </a>
                                            <div class="media-body">
                                                <h5>{{$item->product->title}}</h5>
                                                版本：{{$item->productSku->title}}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="sku-price text-center">￥{{$item->price}}</td>
                                    <td class="text-center">{{$item->amount}}</td>
                                    {{-- $index === 0就是当前第一组订单，总价只显示这里一次 --}}
                                    @if ($index === 0)
                                    {{-- 商品总价 --}}
                                    <td rowspan="{{count($order->items)}}" class="text-center">
                                        <b>￥{{$order->total_amount}}</b>
                                    </td>
                                    {{-- 订单状态 --}}
                                    <td rowspan="{{ count($order->items) }}" class="text-center">
                                        @if ($order->paid_at)
                                            @if ($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                                                已支付
                                            @else
                                                {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
                                            @endif
                                        @elseif($order->closed)
                                            已关闭
                                        @else
                                            未支付<br>
                                            请于 {{$order->created_at->addSeconds(config('app.order_ttl'))->format('Y-m-d H:i')}}<br>
                                            前完成支付<br>
                                            否则订单将自动关闭
                                        @endif
                                    </td>
                                    <td rowspan="{{count($order->items)}}" class="text-center"><a href="{{route('orders.show', $order)}}">查看订单</a></td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@stop