<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $delay)
    {
        $this->order = $order;
        //调用laravel封装的delay
        //设置延迟的时间，delay() 方法的参数代表多少秒之后执行
        $this->delay($delay);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //paid_at支付时间，如果被支付则返回
        if($this->order->paid_at) return;

        //通过事务执行sql
        DB::transaction(function(){
            //将订单的closed字段设置为true, 即关闭订单
            $this->order->update(['closed' => true]);
            //循环遍历订单中的商品SKU, 将订单中的数量加回到sku库存中
            //items是order模型方法，belongsto(orderItmes::class)
            foreach($this->order->items as $item){
                $item->productSku->addStock($item->amount);
            }
        });
    }
}
