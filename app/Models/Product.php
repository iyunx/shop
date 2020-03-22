<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['title', 'description', 'image', 'on_sale', 'rating', 'sold_count', 'review_count', 'price'];

    protected $casts = ['on_sale'=>'boolean'];

    //与商品sku关联
    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }

    //访问器，商品图片访问器
    public function getImageAttribute()
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (\Str::startsWith($this->attributes['image'], ['http://', 'https://'])) {
            return $this->attributes['image'];
        }
        //这里 \Storage::disk('admin') 的参数 public 需要和我们在 config/admin.php 里面的 upload.disk 配置一致。
        return \Storage::disk('admin')->url($this->attributes['image']);
    }
}
