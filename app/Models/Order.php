<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    const SHIP_STATUS_PENDING = 'pending';
    const SHIP_STATUS_DELIVERED = 'delivered';
    const SHIP_STATUS_RECEIVED = 'received';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => '未退款',
        self::REFUND_STATUS_APPLIED    => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS    => '退款成功',
        self::REFUND_STATUS_FAILED     => '退款失败',
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING   => '未发货',
        self::SHIP_STATUS_DELIVERED => '已发货',
        self::SHIP_STATUS_RECEIVED  => '已收货',
    ];

    protected $fillable = [
        'no',
        'address',
        'total_amount',
        'remark',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_status',
        'refund_no',
        'closed',
        'reviewed',
        'ship_status',
        'ship_data',
        'extra',
    ];

    /**
     * laravel $casts 属性类型转换
     * 提供了一个便利的方法来将属性转换为常见的数据类型
     */
    protected $casts = [
        'closed'    => 'boolean',
        'reviewed'  => 'boolean',
        'address'   => 'json',
        'ship_data' => 'json',
        'extra'     => 'json',
    ];

    /**
     * laravel $dates
     * 日期格式转换器.
     * @var array
     */
    protected $dates = [
        'paid_at',
    ];

    //boot() 方法中注册了一个模型创建事件监听函数
    protected static function boot()
    {
        parent::boot();
        //监听模型创建，写入数据库之前
        static::creating(function($model){
            //如果order模型的no字段为空
            if($model->no){
                //findAvailableNo() 为自定义的方法
                $model->no = static::findAvailableNo();
                //如果订单流水号no字段创建失败，则终止创建订单
                if(!$model->no) return;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function findAvailableNo()
    {
        //订单流水号前缀
        $prefix = date('YmdHis');
        for($i=0; $i<10; $i++){
            //随机生成6位的数字
            //str_pad（短信验证码或其他，6长度的验证码，不足6为用0填充，填充在左侧STR_PAD_LEFT） random_int随机整数
            $no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            //判断是否已经存在
            if(!static::query()->where('no', $no)->exists()){
                return $no;
            }
        }
        Log::warning('find order no failed');

        return false;
    }
}
