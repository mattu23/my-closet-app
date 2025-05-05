<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * 一括代入可能な属性
     * 
     * @var array
     */
    protected $fillable = [
        'name',        // カテゴリー名
        'slug',        // URL用の文字列（自動生成）
        'description', // カテゴリーの説明
        'parent_id',   // 親カテゴリーのID
        'path',        // 階層パス（自動生成）
        'level'        // 階層の深さ（自動生成）
    ];

    /**
     * 親カテゴリーを取得
     * pathから親カテゴリーのIDを抽出して取得
     * 例：pathが"1/5/8"の場合、ID:5のカテゴリーを取得
     */
    public function getParentAttribute()
    {
        if (!$this->path) return null;
        $pathParts = explode('/', $this->path);
        if (count($pathParts) < 2) return null;
        $parentId = $pathParts[count($pathParts) - 2];
        return self::find($parentId);
    }

    /**
     * 直接の子カテゴリーを取得
     * 現在のカテゴリーの直下にあるカテゴリーのみを取得
     * 例：トップス（ID:1）の子カテゴリーとしてTシャツ、シャツ、ニットを取得
     */
    public function getChildrenAttribute()
    {
        return self::where('path', 'LIKE', $this->path . '/%')
            ->where('level', $this->level + 1)
            ->get();
    }

    /**
     * 全ての子カテゴリーを再帰的に取得
     * 現在のカテゴリーの下にある全てのカテゴリーを取得
     * 例：トップス（ID:1）の下にある全てのカテゴリーを取得
     */
    public function getAllChildrenAttribute()
    {
        return self::where('path', 'LIKE', $this->path . '/%')
            ->orderBy('level')
            ->get();
    }

    /**
     * このカテゴリーに属する洋服を取得
     */
    public function clothes()
    {
        return $this->hasMany(Clothes::class);
    }

    /**
     * モデルの初期化処理
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * カテゴリー作成時の処理
         * 1. スラッグの自動生成（日本語はローマ字に変換）
         * 2. パスとレベルの自動設定
         */
        static::creating(function ($category) {
            // スラッグの自動生成
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }

            // パスとレベルの設定
            if ($category->parent_id) {
                $parent = self::find($category->parent_id);
                $category->path = $parent->path ? $parent->path . '/' . $parent->id : $parent->id;
                $category->level = $parent->level + 1;
            } else {
                $category->path = null;
                $category->level = 1;
            }
        });

        /**
         * カテゴリー更新時の処理
         * 親カテゴリーが変更された場合、パスとレベルを更新
         */
        static::updating(function ($category) {
            // パスとレベルの更新
            if ($category->isDirty('parent_id')) {
                if ($category->parent_id) {
                    $parent = self::find($category->parent_id);
                    $category->path = $parent->path ? $parent->path . '/' . $parent->id : $parent->id;
                    $category->level = $parent->level + 1;
                } else {
                    $category->path = null;
                    $category->level = 1;
                }
            }
        });
    }
} 