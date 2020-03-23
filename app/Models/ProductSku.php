<?php

namespace App\Models;

use App\Exceptions\InternalException;
use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    protected $fillable = ['title', 'description', 'price', 'stock'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    //商品库存，下订单后减少
    public function decreaseStock($amount)
    {
        if($amount < 0) throw new InternalException('减库存不可小于0');

        //decrement('表字段名'， '自减量') 默认自减1
        return $this->where('id', $this->id)->where('stock', '>=', $amount)->decrement('stock', $amount);
    }

    //提交订单后，有一个过期时间，过期后，订单取消，订单中的商品数量，返回给库存
    public function addStock($amount)
    {
        if($amount<0) throw new InternalException('加库存不可小于0');

        //increment('表字段名'， '自增量') 默认每次加1
        return $this->increment('stock', $amount);
    }

}
