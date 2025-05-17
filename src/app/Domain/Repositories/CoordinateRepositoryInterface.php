<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Coordinate;
use App\Application\DTOs\CoordinateDTO;

interface CoordinateRepositoryInterface
{
    /**
     * コーディネートを作成
     */
    public function create(CoordinateDTO $dto): Coordinate;

    /**
     * コーディネートを更新
     */
    public function update(int $id, CoordinateDTO $dto): Coordinate;

    /**
     * コーディネートを削除
     */
    public function delete(int $id): void;

    /**
     * IDでコーディネートを取得
     */
    public function findById(int $id): ?Coordinate;

    /**
     * ユーザーIDでコーディネートを取得
     */
    public function findByUserId(int $userId): array;

    /**
     * コーディネートに洋服を追加
     */
    public function addClothes(int $coordinateId, int $clothesId): void;

    /**
     * コーディネートから洋服を削除
     */
    public function removeClothes(int $coordinateId, int $clothesId): void;

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
     * コーディネートと洋服の関連付けを保存
     */
    public function saveClothesRelation(int $coordinateId, array $clothesIds): void;
} 