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
                    @if (!$item->productSku->product->on_sale)
                    <span class="warning">该商品已经下架</span>
                    @elseif($item->productSku->stock < 1)
                    <span class="warning">该商品库存不足</span>
                    @else
                    <span class="warning">库存：{{$item->productSku->stock}}</span>
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
    <!-- 开始 -->
    <div>
      <form class="form-horizontal" role="form" id="order-form">
        <div class="form-group row">
            <label class="col-form-label col-sm-3 text-md-right">选择收货地址</label>
            <div class="col-sm-9 col-md-7">
            <select class="form-control" name="address">
                @foreach($addresses as $address)
                <option value="{{ $address->id }}">{{ $address->full_address }} {{$address->contact_name}} {{$address->contact_phone}}</option>
                @endforeach
            </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-form-label col-sm-3 text-md-right">备注</label>
            <div class="col-sm-9 col-md-7">
            <textarea name="remark" class="form-control" rows="3"></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="offset-sm-3 col-sm-3">
            <button type="button" class="btn btn-primary btn-create-order">提交订单</button>
            </div>
        </div>
      </form>
    </div>
    <!-- 结束 -->
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

    //提交订单
    $('.btn-create-order').click(function(){
        //要提交给后台的数据结合
        let req = {
            address_id: $('#order-form').find('select[name=address]').val(),
            items: [],
            remark: $('#order-form').find('textarea[name=remark]').val()
        };
        //遍历<table>标签中带有data-id属性的tr标签，此标签id为sku
        $('table tr[data-id]').each(function(){
            // 获取当前行的单选框
            let $checkbox = $(this).find('input[name=select][type=checkbox]');
            //如果单选框被禁用或没有被选中的则跳过
            if( $checkbox.prop('disabled') || !$checkbox.prop('checked') ) return;

            //获取当前输入框购买商品的数量
            let $input = $(this).find('input[name=amount]').val();
            // 如果用户将数量设为 0 或者不是一个数字，则也跳过
            if($input == 0 || isNaN($input) ) return;
            
            //把sku id和数量存入请求参数items中
            req.items.push({
                sku_id:$(this).data('id'),
                amount:$input
            });
        });

        axios.post("{{route('orders.store')}}", req)
             .then(res=>{
                swal('订单提交成功', '', 'success'),
                setTimeout(()=>location.href="/orders/"+res.data.id, 2000)
             })
             .catch(error=>{
                if(error.response.status === 401) return swal('请登录', '', 'warning');
                if(error.response.status === 422){
                    let html = '';
                    $.each(error.response.data.errors, (index, error)=>html = error[0]);
                    return swal(html, '', 'warning');
                }
                return swal('系统错误', '', 'error');
             })

    });
</script>
@stop