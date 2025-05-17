<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Category;

interface CategoryRepositoryInterface
{
    /**
     * カテゴリーをIDで検索
     */
    public function findById(int $id): ?Category;

    /**
     * カテゴリーを作成
     */
    public function create(Category $category): Category;

    /**
     * カテゴリーを更新
     */
    public function update(int $id, Category $category): Category;

    /**
     * ルートカテゴリーを取得
     * @return Category[]
     */
    public function findRootCategories(): array;

    /**
     * 特定の親カテゴリーに属するカテゴリーを取得
     * @return Category[]
     */
    public function findByParentId(int $parentId): array;

    /**
     * 特定のカテゴリーの子カテゴリーを全て取得（再帰的）
     * @return Category[]
     */
    public function findAllChildren(int $categoryId): array;
} 