@extends('layouts.app')
@section('title', $product->title)

@section('content')
<div class="row">
<div class="col-lg-12">
<div class="card">
  <div class="card-body product-info">
    <div class="row">
      <div class="col-5">
        <img class="cover" src="{{ $product->image }}" alt="">
      </div>
      <div class="col-7">
        <div class="title">{{ $product->title }}</div>
        <div class="price"><label>价格</label><em>￥</em><span>{{ $product->price }}</span></div>
        <div class="sales_and_reviews">
          <div class="sold_count">累计销量 <span class="count">{{ $product->sold_count }}</span></div>
          <div class="review_count">累计评价 <span class="count">{{ $product->review_count }}</span></div>
          <div class="rating" title="评分 {{ $product->rating }}">评分 <span class="count">{{str_repeat('★', $product->rating)}}{{ str_repeat('☆', 5 - floor($product->rating)) }}</span></div>
        </div>
        <div class="skus">
          <label>选择</label>
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            @foreach($product->skus as $sku)
              <label class="btn sku-btn {{ $loop->first ? 'active' : '' }}" title="{{ $sku->description }}" 
                data-price="{{$sku->price}}" 
                stock="{{$sku->stock}}" 
                data-toggle="tooltip">
                <input type="radio" name="skus" autocomplete="off" value="{{ $sku->id }}"> {{ $sku->title }}
              </label>
            @endforeach
          </div>
        </div>
        <div class="cart_amount"><label>数量</label><input type="text" class="form-control form-control-sm" value="1"><span>件</span><span class="stock"></span></div>
        <div class="buttons">
          <button class="btn btn-favor {{$favored?'btn-danger':'btn-success'}}">{{$favored?'取消收藏':'❤ 收藏'}}</button>
          <button class="btn btn-primary btn-add-to-cart">加入购物车</button>
        </div>
      </div>
    </div>
    <div class="product-detail">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" href="#product-detail-tab" aria-controls="product-detail-tab" role="tab" data-toggle="tab" aria-selected="true">商品详情</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#product-reviews-tab" aria-controls="product-reviews-tab" role="tab" data-toggle="tab" aria-selected="false">用户评价</a>
        </li>
      </ul>
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="product-detail-tab">
          {!! $product->description !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="product-reviews-tab">
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
@stop

@section('js')
<style>
  .swal-modal {
    margin-top: -200px;
  }
</style>
<script>
  $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});
  $('.sku-btn').click(function () {
    //这里可以用attr 或data获取对应模块的其他数据
    $('.product-info .price span').text($(this).data('price'));
    $('.product-info .stock').html('库存：' + $(this).attr('stock') + '件');
  });

  //商品收藏或取消
  $('.btn-favor').click(function(){
    axios.{{$favored?'delete':'post'}}("{{route('products.favor', $product->id)}}")
         .then(()=>{
           swal('操作成功', '', 'success');
           setTimeout(()=>location.reload(), 2000)
          })
         .catch((error)=>{
          if(error.response && error.response.status === 401){
            swal('请先登录', '', 'error');
          }else if(error.response && (error.response.data.msg || error.response.data.message)){
            swal(error.response.data.msg ? error.response.data.msg : error.response.data.message, '', 'error')
          }else{
            swal('系统错误', '', 'error')
          }
         })
  });
</script>
@stop