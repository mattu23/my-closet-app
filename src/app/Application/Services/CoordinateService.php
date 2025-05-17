<?php

namespace App\Application\Services;

use App\Domain\Entities\Coordinate;
use App\Domain\Repositories\CoordinateRepositoryInterface;
use App\Domain\Repositories\ClothesRepositoryInterface;
use App\Application\DTOs\CoordinateDTO;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

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
     * コーディネート一覧データを取得
     */
    public function getCoordinatesData(): array
    {
        $userId = Auth::id();
        return [
            'coordinates' => $this->coordinateRepository->findByUserId($userId)
        ];
    }

    /**
     * 作成フォーム用のデータを取得
     */
    public function getCreateFormData(): array
    {
        $userId = Auth::id();
        return [
            'clothes' => $this->clothesRepository->findByUserId($userId)
        ];
    }

    /**
     * コーディネートを作成
     */
    public function createCoordinate(array $validated, ?UploadedFile $image): void
    {
        $imagePath = null;
        if ($image) {
            $imagePath = $image->store('coordinates', 'public');
        }

        $userId = Auth::id();
        $dto = new CoordinateDTO(
            $validated['name'],
            $validated['description'],
            $imagePath,
            $userId,
            $validated['clothes_ids']
        );

        $this->coordinateRepository->create($dto);
    }

    /**
     * コーディネート詳細を取得
     */
    public function getCoordinateDetail(int $id): array
    {
        $userId = Auth::id();
        $coordinate = $this->coordinateRepository->findById($id);
        
        if (!$coordinate || $coordinate->getUserId() !== $userId) {
            throw new \Exception('コーディネートが見つかりません。');
        }

        return [
            'coordinate' => $coordinate,
            'clothes' => $this->clothesRepository->findByCoordinateId($id)
        ];
    }

    /**
     * 編集フォーム用のデータを取得
     */
    public function getEditFormData(int $id): array
    {
        $userId = Auth::id();
        $coordinate = $this->coordinateRepository->findById($id);
        
        if (!$coordinate || $coordinate->getUserId() !== $userId) {
            throw new \Exception('コーディネートが見つかりません。');
        }

        return [
            'coordinate' => $coordinate,
            'clothes' => $this->clothesRepository->findByUserId($userId),
            'selectedClothes' => $this->clothesRepository->findByCoordinateId($id)
        ];
    }

    /**
     * コーディネートを更新
     */
    public function updateCoordinate(int $id, array $validated, ?UploadedFile $image): void
    {
        $userId = Auth::id();
        $coordinate = $this->coordinateRepository->findById($id);
        
        if (!$coordinate || $coordinate->getUserId() !== $userId) {
            throw new \Exception('コーディネートが見つかりません。');
        }

        $imagePath = $coordinate->getImagePath();
        if ($image) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $image->store('coordinates', 'public');
        }

        $dto = new CoordinateDTO(
            $validated['name'],
            $validated['description'],
            $imagePath,
            $userId,
            $validated['clothes_ids']
        );

        $this->coordinateRepository->update($id, $dto);
    }

    /**
     * コーディネートを削除
     */
    public function deleteCoordinate(int $id): void
    {
        $userId = Auth::id();
        $coordinate = $this->coordinateRepository->findById($id);
        
        if (!$coordinate || $coordinate->getUserId() !== $userId) {
            throw new \Exception('コーディネートが見つかりません。');
        }

        if ($coordinate->getImagePath()) {
            Storage::disk('public')->delete($coordinate->getImagePath());
        }

        $this->coordinateRepository->delete($id);
    }

    /**
     * コーディネートに洋服を追加
     */
    public function addClothesToCoordinate(int $coordinateId, int $clothesId): void
    {
        $userId = Auth::id();
        $coordinate = $this->coordinateRepository->findById($coordinateId);
        
        if (!$coordinate || $coordinate->getUserId() !== $userId) {
            throw new \Exception('コーディネートが見つかりません。');
        }

        $clothes = $this->clothesRepository->findById($clothesId);
        if (!$clothes || $clothes->getUserId() !== $userId) {
            throw new \Exception('洋服が見つかりません。');
        }

        $this->coordinateRepository->addClothes($coordinateId, $clothesId);
    }

    /**
     * コーディネートから洋服を削除
     */
    public function removeClothesFromCoordinate(int $coordinateId, int $clothesId): void
    {
        $userId = Auth::id();
        $coordinate = $this->coordinateRepository->findById($coordinateId);
        
        if (!$coordinate || $coordinate->getUserId() !== $userId) {
            throw new \Exception('コーディネートが見つかりません。');
        }

        $this->coordinateRepository->removeClothes($coordinateId, $clothesId);
    }
} 