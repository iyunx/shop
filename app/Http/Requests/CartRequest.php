<?php

namespace App\Http\Requests;

use App\Models\ProductSku;
use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 我们将商品添加到购物车的时候会提交两个参数：
     * 商品 SKU ID；
     * 购买数量。
     * @return array
     */
    public function rules()
    {
        return [
            'sku_id' => [
                'required', function($attribute, $value, $fail){
                    if(!$sku = ProductSku::find($value)) return $fail('该商品不存在');
                    if(!$sku->product->on_sale) return $fail('该商品未上架');
                    if($sku->stock == 0) return $fail('该商品已售罄');
                    if($this->amount > 0 && $sku->stock < $this->input('amount')) return $fail('该商品库存不足');
                    if(!preg_match("/^[1-9][0-9]*$/" ,$this->amount)) return $fail('请输入正数');
                }
            ]
        ];
    }

    public function attributes()
    {
        return [
            'amount' => '商品数量'
        ];
    }

    public function messages()
    {
        return [
            'sku_id.required' => '请选择商品'
        ];
    }
}
