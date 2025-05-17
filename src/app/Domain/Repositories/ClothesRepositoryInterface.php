<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Clothes;

interface ClothesRepositoryInterface
{
    /**
     * 洋服を保存
     */
    public function save(Clothes $clothes): void;

    /**
     * IDで洋服を取得
     */
    public function findById(int $id): ?Clothes;

    /**
     * ユーザーIDで洋服を取得
     */
    public function findByUserId(int $userId): array;

    /**
     * カテゴリーIDで洋服を取得
     */
    public function findByCategoryId(int $categoryId): array;

    /**
     * カテゴリーとその子カテゴリーに属する洋服を取得
     */
    public function findByCategoryAndChildren(int $categoryId): array;

    /**
     * コーディネートIDで洋服を取得
     */
    public function findByCoordinateId(int $coordinateId): array;

    /**
     * 洋服を削除
     */
    public function delete(Clothes $clothes): void;
} 