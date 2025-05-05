<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coordinate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'image_path',
        'top_id',
        'bottom_id',
        'outer_id',
        'shoes_id',
        'accessory_id',
    ];

    /**
     * このコーディネートを所有するユーザーを取得
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * このコーディネートで使用されているトップス
     */
    public function top()
    {
        return $this->belongsTo(Clothes::class, 'top_id');
    }

    /**
     * このコーディネートで使用されているボトムス
     */
    public function bottom()
    {
        return $this->belongsTo(Clothes::class, 'bottom_id');
    }

    /**
     * このコーディネートで使用されているアウター
     */
    public function outer()
    {
        return $this->belongsTo(Clothes::class, 'outer_id');
    }

    /**
     * このコーディネートで使用されている靴
     */
    public function shoes()
    {
        return $this->belongsTo(Clothes::class, 'shoes_id');
    }

    /**
     * このコーディネートで使用されているアクセサリー
     */
    public function accessory()
    {
        return $this->belongsTo(Clothes::class, 'accessory_id');
    }
} 