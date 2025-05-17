<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Coordinate;

interface CoordinateRepositoryInterface
{
    /**
     * コーディネートをIDで検索
     */
    public function findById(int $id): ?Coordinate;

    /**
     * 特定のユーザーの全てのコーディネートを取得
     * @return Coordinate[]
     */
    public function findByUserId(int $userId): array;

    /**
     * 特定の洋服を含むコーディネートを取得
     * @return Coordinate[]
     */
    public function findByClothesId(int $clothesId): array;

    /**
     * コーディネートを保存（新規作成または更新）
     */
    public function save(Coordinate $coordinate): void;

    /**
     * コーディネートを削除
     */
    public function delete(Coordinate $coordinate): void;

    /**
     * コーディネートと洋服の関連付けを保存
     */
    public function saveClothesRelation(int $coordinateId, array $clothesIds): void;
} 