@extends('layouts.app')
@section('title', '收货地址')
@section('content')
<div class="row">
    <div class="col-md-10 offset-1">
        <div class="card">
            <div class="card-header">
                <a class="float-right" href="{{route('address.create')}}">
                    新增收货地址
                </a>
                <span>
                    收货地址
                </span>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>收货人</th>
                            <th>地址</th>
                            <th>邮编</th>
                            <th>电话</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($address as $item)
                            <tr>
                                <td scope="row">{{$item->contact_name}}</td>
                                <td>{{$item->full_address}}</td>
                                <td>{{$item->zip}}</td>
                                <td>{{$item->contact_phone}}</td>
                                <td>
                                    <a href="{{route('address.edit', $item)}}" class="btn btn-primary">修改</a>
                                    <button type="button" class="btn btn-danger del-address" del='{{$item->id}}'>删除</button>
                                </td>
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
    $('.del-address').click(function(){
        let id = $(this).attr('del');
        console.log(id);
        //引入sweetalert弹出层
        swal({
            title: '确定要删除吗？',
            icon: 'warning',
            buttons: ['取消', '确定'],
            dangerMode: true
        })
        .then((willDelete)=>{// 用户点击按钮后会触发这个回调函数
            // 用户点击确定 willDelete 值为 true， 否则为 false
            // 用户点了取消，啥也不做
            if(!willDelete) return;

            axios.delete("address/"+ id)
                 .then(()=>location.reload());
        })
    });
</script>
@stop