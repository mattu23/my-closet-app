<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Clothes;

interface ClothesRepositoryInterface
{
    /**
     * 洋服をIDで検索
     */
    public function findById(int $id): ?Clothes;

    /**
     * 特定のユーザーの全ての洋服を取得
     * @return Clothes[]
     */
    public function findByUserId(int $userId): array;

    /**
     * 特定のカテゴリーに属する洋服を取得
     * @return Clothes[]
     */
    public function findByCategoryId(int $categoryId): array;

    /**
     * 特定のカテゴリーとその子カテゴリーに属する全ての洋服を取得
     * @return Clothes[]
     */
    public function findByCategoryAndChildren(int $categoryId): array;

    /**
     * 洋服を保存（新規作成または更新）
     */
    public function save(Clothes $clothes): void;

    /**
     * 洋服を削除
     */
    public function delete(Clothes $clothes): void;
} 