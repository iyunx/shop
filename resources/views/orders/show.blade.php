@extends('layouts.app')
@section('title', '查看订单')

@section('content')
<div class="row">
    <div class="col-lg-10 offset-1">
        <div class="card">
            <div class="card-header">
                订单编号：{{$order->no}}
                <span class="float-right">下单时间：{{$order->created_at}}</span>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>商品信息</th>
                            <th class="text-center">单价</th>
                            <th class="text-center">数量</th>
                            <th class="text-center">小计</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $index=>$item)
                        <tr>
                            <td scope="row">
                                <div class="media">
                                    <a class="d-flex mr-3" href="#">
                                        <img src="{{$item->product->image}}" alt="" width="80">
                                    </a>
                                    <div class="media-body">
                                        <h5>{{$item->product->title}}</h5>
                                        型号：{{$item->productSku->title}}
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">{{$item->price}}</td>
                            <td class="text-center">{{$item->amount}}</td>
                            @if ($index === 0)
                                <td rowspan="2" class="text-center"><b>￥{{$order->total_amount}}</b></td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3"><b>收货地址：</b>{{join(' ', $order->address)}}</div>
                        <div class="mb-3"><b>订单编号：</b>{{$order->no}}</div>
                        <div><b>备注：</b>{{$order->remark??'-'}}</div>
                    </div>
                    <div class="col-md-6 text-center" style="border-left:1px solid #999">
                        <div class="mb-3">订单总价：<b>￥{{$order->total_amount}}</b></div>
                        <div>支付状态：
                            @if ($order->paid_at)
                                @if ($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                                    已支付
                                @else
                                    {{\App\Models\Order::$refundStatusMap[$order->refund_status]}}
                                @endif
                            @elseif($order->closed)
                                已关闭
                            @else
                                未支付
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop