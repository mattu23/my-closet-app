<?php

namespace App\Application\Services;

use App\Domain\Entities\Coordinate;
use App\Domain\Repositories\CoordinateRepositoryInterface;
use App\Domain\Repositories\ClothesRepositoryInterface;

class CoordinateService
{
    private CoordinateRepositoryInterface $coordinateRepository;
    private ClothesRepositoryInterface $clothesRepository;

    public function __construct(
        CoordinateRepositoryInterface $coordinateRepository,
        ClothesRepositoryInterface $clothesRepository
    ) {
        $this->coordinateRepository = $coordinateRepository;
        $this->clothesRepository = $clothesRepository;
    }

    /**
     * コーディネートを作成
     */
    public function createCoordinate(
        string $name,
        string $description,
        ?string $imagePath,
        array $clothesIds,
        int $userId
    ): Coordinate {
        $coordinate = new Coordinate(
            0, // 仮のID（リポジトリで実際のIDが設定される）
            $name,
            $description,
            $imagePath,
            $clothesIds,
            $userId
        );

        $this->coordinateRepository->save($coordinate);
        return $coordinate;
    }

    /**
     * コーディネートを更新
     */
    public function updateCoordinate(
        int $id,
        string $name,
        string $description,
        ?string $imagePath,
        array $clothesIds
    ): ?Coordinate {
        $coordinate = $this->coordinateRepository->findById($id);
        if (!$coordinate) {
            return null;
        }

        $coordinate->changeName($name);
        $coordinate->changeDescription($description);
        $coordinate->changeImage($imagePath);
        $coordinate->setClothes($clothesIds);

        $this->coordinateRepository->save($coordinate);
        return $coordinate;
    }

    /**
     * コーディネートを削除
     */
    public function deleteCoordinate(int $id): bool
    {
        $coordinate = $this->coordinateRepository->findById($id);
        if (!$coordinate) {
            return false;
        }

        $coordinate->delete();
        $this->coordinateRepository->save($coordinate);
        return true;
    }

    /**
     * ユーザーのコーディネート一覧を取得
     * @return Coordinate[]
     */
    public function getCoordinatesByUserId(int $userId): array
    {
        return $this->coordinateRepository->findByUserId($userId);
    }

    /**
     * 特定の洋服を含むコーディネート一覧を取得
     * @return Coordinate[]
     */
    public function getCoordinatesByClothesId(int $clothesId): array
    {
        return $this->coordinateRepository->findByClothesId($clothesId);
    }

    /**
     * コーディネートに洋服を追加
     */
    public function addClothesToCoordinate(int $coordinateId, int $clothesId): bool
    {
        $coordinate = $this->coordinateRepository->findById($coordinateId);
        if (!$coordinate) {
            return false;
        }

        $clothes = $this->clothesRepository->findById($clothesId);
        if (!$clothes) {
            return false;
        }

        $coordinate->addClothes($clothesId);
        $this->coordinateRepository->save($coordinate);
        return true;
    }

    /**
     * コーディネートから洋服を削除
     */
    public function removeClothesFromCoordinate(int $coordinateId, int $clothesId): bool
    {
        $coordinate = $this->coordinateRepository->findById($coordinateId);
        if (!$coordinate) {
            return false;
        }

        $coordinate->removeClothes($clothesId);
        $this->coordinateRepository->save($coordinate);
        return true;
    }
} 