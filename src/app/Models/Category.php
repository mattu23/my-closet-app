<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'level',       // 階層の深さ（自動生成）
        'user_id',     // カテゴリーを所有するユーザーのID
    ];

    /**
     * このカテゴリーの親カテゴリー
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * このカテゴリーの子カテゴリー
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * このカテゴリーに属する洋服
     */
    public function clothes(): HasMany
    {
        return $this->hasMany(Clothes::class);
    }

    /**
     * このカテゴリーを所有するユーザー
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ルートカテゴリーかどうか
     */
    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * 子カテゴリーを持つかどうか
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * 全ての子孫カテゴリーを取得（再帰的）
     */
    public function getAllChildren(): array
    {
        $result = [];
        $this->getChildrenRecursive($this->id, $result);
        return $result;
    }

    /**
     * 子カテゴリーを再帰的に取得
     */
    private function getChildrenRecursive(int $parentId, array &$result): void
    {
        $children = Category::where('parent_id', $parentId)->get();
        foreach ($children as $child) {
            $result[] = $child;
            $this->getChildrenRecursive($child->id, $result);
        }
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