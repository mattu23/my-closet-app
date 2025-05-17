<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Clothes extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * 一括代入可能な属性
     */
    protected $fillable = [
        'name',
        'description',
        'image_path',
        'category_id',
        'user_id',
        'size',
        'color_name',
        'color_code',
        'brand_name',
        'brand_description',
        'brand_country',
    ];

    /**
     * このアイテムが属するカテゴリー
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * このアイテムを所有するユーザー
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * このアイテムが含まれるコーディネート
     */
    public function coordinates(): BelongsToMany
    {
        return $this->belongsToMany(Coordinate::class, 'coordinate_clothes');
    }
    
    /**
     * サイズ情報があるかどうか
     */
    public function hasSize(): bool
    {
        return !empty($this->size);
    }
    
    /**
     * 色情報があるかどうか
     */
    public function hasColor(): bool
    {
        return !empty($this->color_name) && !empty($this->color_code);
    }
    
    /**
     * ブランド情報があるかどうか
     */
    public function hasBrand(): bool
    {
        return !empty($this->brand_name);
    }
    
    /**
     * 画像のURLを取得
     */
    public function getImageUrl(): string
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        
        return asset('images/no-image.png');
    }
} 