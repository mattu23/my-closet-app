<?php

namespace App\Domain\Repositories;

interface ClothesRepositoryInterface
{
    /**
     * IDで洋服を検索
     */
    public function findById(int $id): ?array;

    /**
     * ユーザーIDで洋服を検索
     */
    public function findByUserId(int $userId): array;

    /**
     * カテゴリーIDで洋服を検索
     */
    public function findByCategoryId(int $categoryId): array;

    /**
     * カテゴリーとその子カテゴリーで洋服を検索
     */
    public function findByCategoryAndChildren(int $categoryId): array;

    /**
     * コーディネートIDで洋服を検索
     */
    public function findByCoordinateId(int $coordinateId): array;

    /**
     * 洋服を保存
     */
    public function save(array $clothes): void;

    /**
     * 洋服を削除
     */
    public function delete(array $clothes): void;
} 