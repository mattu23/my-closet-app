<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Category;
use App\Domain\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * カテゴリーを作成
     */
    public function create(Category $category): Category
    {
        $id = DB::table('categories')->insertGetId([
            'name' => $category->getName(),
            'description' => $category->getDescription(),
            'parent_id' => $category->getParentId(),
            'user_id' => $category->getUserId(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return $this->findById($id);
    }

    /**
     * カテゴリーを更新
     */
    public function update(int $id, Category $category): Category
    {
        DB::table('categories')
            ->where('id', $id)
            ->update([
                'name' => $category->getName(),
                'description' => $category->getDescription(),
                'parent_id' => $category->getParentId(),
                'updated_at' => now()
            ]);

        return $this->findById($id);
    }

    /**
     * カテゴリーを削除
     */
    public function delete(int $id): void
    {
        DB::table('categories')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);
    }

    /**
     * IDでカテゴリーを取得
     */
    public function findById(int $id): ?Category
    {
        $data = DB::table('categories')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$data) {
            return null;
        }

        return new Category(
            $data->id,
            $data->name,
            $data->description,
            $data->parent_id,
            $data->user_id,
            $data->created_at,
            $data->deleted_at
        );
    }

    /**
     * 親IDでカテゴリーを取得
     */
    public function findByParentId(int $parentId): array
    {
        $data = DB::table('categories')
            ->where('parent_id', $parentId)
            ->whereNull('deleted_at')
            ->get();

        return $data->map(function ($item) {
            return new Category(
                $item->id,
                $item->name,
                $item->description,
                $item->parent_id,
                $item->user_id,
                $item->created_at,
                $item->deleted_at
            );
        })->all();
    }

    /**
     * ルートカテゴリーを取得
     */
    public function getRootCategories(): array
    {
        $data = DB::table('categories')
            ->whereNull('parent_id')
            ->whereNull('deleted_at')
            ->get();

        return $data->map(function ($item) {
            return new Category(
                $item->id,
                $item->name,
                $item->description,
                $item->parent_id,
                $item->user_id,
                $item->created_at,
                $item->deleted_at
            );
        })->all();
    }

    /**
     * 子カテゴリーを取得
     */
    public function getChildren(int $id): array
    {
        $data = DB::table('categories')
            ->where('parent_id', $id)
            ->whereNull('deleted_at')
            ->get();

        return $data->map(function ($item) {
            return new Category(
                $item->id,
                $item->name,
                $item->description,
                $item->parent_id,
                $item->user_id,
                $item->created_at,
                $item->deleted_at
            );
        })->all();
    }
} 