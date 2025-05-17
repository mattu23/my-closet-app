<?php

namespace App\Application\Services;

use App\Domain\Entities\Clothes;
use App\Domain\Repositories\ClothesRepositoryInterface;
use App\Domain\Repositories\CategoryRepositoryInterface;
use App\Domain\ValueObjects\Size;
use App\Domain\ValueObjects\Color;
use App\Domain\ValueObjects\Brand;

class ClothesService
{
    private ClothesRepositoryInterface $clothesRepository;
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        ClothesRepositoryInterface $clothesRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->clothesRepository = $clothesRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * 洋服を作成
     */
    public function createClothes(
        string $name,
        string $description,
        ?string $imagePath,
        int $categoryId,
        int $userId,
        ?string $size = null,
        ?array $colorData = null,
        ?array $brandData = null
    ): Clothes {
        // 値オブジェクトの作成
        $sizeObj = null;
        if ($size) {
            try {
                $sizeObj = new Size($size);
            } catch (\InvalidArgumentException $e) {
                // サイズが無効な場合は無視
            }
        }
        
        $colorObj = null;
        if ($colorData && isset($colorData['name']) && isset($colorData['hex_code'])) {
            try {
                $colorObj = new Color($colorData['name'], $colorData['hex_code']);
            } catch (\InvalidArgumentException $e) {
                // 色が無効な場合は無視
            }
        }
        
        $brandObj = null;
        if ($brandData && isset($brandData['name'])) {
            try {
                $brandObj = new Brand(
                    $brandData['name'],
                    $brandData['description'] ?? null,
                    $brandData['country'] ?? null
                );
            } catch (\InvalidArgumentException $e) {
                // ブランドが無効な場合は無視
            }
        }
        
        $clothes = new Clothes(
            0, // 仮のID（リポジトリで実際のIDが設定される）
            $name,
            $description,
            $imagePath,
            $categoryId,
            $userId,
            $sizeObj,
            $colorObj,
            $brandObj
        );

        $this->clothesRepository->save($clothes);
        return $clothes;
    }

    /**
     * 洋服を更新
     */
    public function updateClothes(
        int $id,
        string $name,
        string $description,
        ?string $imagePath,
        int $categoryId,
        ?string $size = null,
        ?array $colorData = null,
        ?array $brandData = null
    ): ?Clothes {
        $clothes = $this->clothesRepository->findById($id);
        if (!$clothes) {
            return null;
        }

        $clothes->changeName($name);
        $clothes->changeDescription($description);
        $clothes->changeImage($imagePath);
        $clothes->changeCategory($categoryId);
        
        // 値オブジェクトの更新
        if ($size !== null) {
            try {
                $clothes->changeSize(new Size($size));
            } catch (\InvalidArgumentException $e) {
                $clothes->changeSize(null);
            }
        }
        
        if ($colorData !== null) {
            if (isset($colorData['name']) && isset($colorData['hex_code'])) {
                try {
                    $clothes->changeColor(new Color($colorData['name'], $colorData['hex_code']));
                } catch (\InvalidArgumentException $e) {
                    $clothes->changeColor(null);
                }
            } else {
                $clothes->changeColor(null);
            }
        }
        
        if ($brandData !== null) {
            if (isset($brandData['name'])) {
                try {
                    $clothes->changeBrand(new Brand(
                        $brandData['name'],
                        $brandData['description'] ?? null,
                        $brandData['country'] ?? null
                    ));
                } catch (\InvalidArgumentException $e) {
                    $clothes->changeBrand(null);
                }
            } else {
                $clothes->changeBrand(null);
            }
        }

        $this->clothesRepository->save($clothes);
        return $clothes;
    }

    /**
     * 洋服を削除
     */
    public function deleteClothes(int $id): bool
    {
        $clothes = $this->clothesRepository->findById($id);
        if (!$clothes) {
            return false;
        }

        $clothes->delete();
        $this->clothesRepository->save($clothes);
        return true;
    }

    /**
     * ユーザーの洋服一覧を取得
     * @return Clothes[]
     */
    public function getClothesByUserId(int $userId): array
    {
        return $this->clothesRepository->findByUserId($userId);
    }

    /**
     * カテゴリーに属する洋服一覧を取得
     * @return Clothes[]
     */
    public function getClothesByCategoryId(int $categoryId): array
    {
        return $this->clothesRepository->findByCategoryId($categoryId);
    }

    /**
     * カテゴリーとその子カテゴリーに属する洋服一覧を取得
     * @return Clothes[]
     */
    public function getClothesByCategoryAndChildren(int $categoryId): array
    {
        return $this->clothesRepository->findByCategoryAndChildren($categoryId);
    }
    
    /**
     * 特定のサイズの洋服を取得
     * @return Clothes[]
     */
    public function getClothesBySize(int $userId, string $size): array
    {
        $allClothes = $this->getClothesByUserId($userId);
        $sizeObj = new Size($size);
        
        return array_filter($allClothes, function (Clothes $clothes) use ($sizeObj) {
            return $clothes->hasSize() && $clothes->getSize()->equals($sizeObj);
        });
    }
    
    /**
     * 特定のブランドの洋服を取得
     * @return Clothes[]
     */
    public function getClothesByBrand(int $userId, string $brandName): array
    {
        $allClothes = $this->getClothesByUserId($userId);
        
        return array_filter($allClothes, function (Clothes $clothes) use ($brandName) {
            return $clothes->hasBrand() && $clothes->getBrand()->containsKeyword($brandName);
        });
    }
    
    /**
     * 暗い色の洋服を取得
     * @return Clothes[]
     */
    public function getDarkColoredClothes(int $userId): array
    {
        $allClothes = $this->getClothesByUserId($userId);
        
        return array_filter($allClothes, function (Clothes $clothes) {
            return $clothes->hasColor() && $clothes->getColor()->isDark();
        });
    }
    
    /**
     * 明るい色の洋服を取得
     * @return Clothes[]
     */
    public function getBrightColoredClothes(int $userId): array
    {
        $allClothes = $this->getClothesByUserId($userId);
        
        return array_filter($allClothes, function (Clothes $clothes) {
            return $clothes->hasColor() && $clothes->getColor()->isBright();
        });
    }
} 