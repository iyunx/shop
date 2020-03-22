@extends('layouts.app')
@section('title', '购物车')

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-header">我的购物车</div>
  <div class="card-body">
    <table class="table table-striped">
      <thead>
      <tr>
        <th><input type="checkbox" id="select-all"></th>
        <th>商品信息</th>
        <th>单价</th>
        <th>数量</th>
        <th>操作</th>
      </tr>
      </thead>
      <tbody class="product_list">
      @foreach ($cartItems as $item)
          <tr data-id="{{$item->productSku->id}}">
            <td><input type="checkbox" name="select" {{$item->productSku->product->on_sale ? 'checked' : 'disabled'}} value="{{$item->productSku->id}}"></td>
            <td class="product_info">
                <div class="preview">
                    <a href="{{route('products.show', $item->productSku->product)}}"><img src="{{$item->productSku->product->image}}"></a>
                </div>
                <div @if(!$item->productSku->product->on_sale) class="not_on_sale" @endif>
                    <span class="product_title">{{$item->productSku->product->title}}</span>
                    <span class="sku_title">{{$item->productSku->title}}</span>
                    @if (!$item->productSku->product->on_sale || !$item->productSku->stock)
                    <span class="warning">该商品已经下架</span>
                    @endif
                </div>
            </td>
            <td>{{$item->productSku->price}}</td>
            <td>
                <input type="number" name="amount" value="{{$item->amount}}" style="width:50px" {{$item->productSku->product->on_sale ?'': 'disabled'}}>
            </td>
            <td><button type="button" class="btn btn-sm btn-danger">移除</button></td>
          </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
</div>
</div>
@stop

@section('js')
<script>
    //商品移除
    $('.btn-danger').click(function(){
        let id = $(this).closest('tr').data('id');
        swal({
            title: '确定要删除吗？',
            icon: 'warning',
            buttons: ['取消', '确定'],
            dangerMode: true
        })
        .then((fds)=>{
            if(!fds) return;
            axios.delete("/cart/"+id).then(()=>location.reload())
        })
    });

    //全选按钮
    $('#select-all').click(function(){
        let checked = $(this).prop('checked');
        let sub = $('input[name=select][type=checkbox]:not([disabled])')
        sub.each(function(){
            $(this).prop('checked', checked);
        });
    });
</script>
@stop