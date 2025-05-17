<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Category;

interface CategoryRepositoryInterface
{
    /**
     * カテゴリーを作成
     */
    public function create(Category $category): Category;

    /**
     * カテゴリーを更新
     */
    public function update(int $id, Category $category): Category;

    /**
     * カテゴリーを削除
     */
    public function delete(int $id): void;

    /**
     * IDでカテゴリーを取得
     */
    public function findById(int $id): ?Category;

    /**
     * 親IDでカテゴリーを取得
     */
    public function findByParentId(int $parentId): array;

    /**
     * ルートカテゴリーを取得
     */
    public function getRootCategories(): array;

    /**
     * 子カテゴリーを取得
     */
    public function getChildren(int $id): array;
} 