<?php

namespace App\Application\Services;

use App\Domain\Entities\Clothes;
use App\Domain\Repositories\ClothesRepositoryInterface;
use App\Domain\Repositories\CategoryRepositoryInterface;
use App\Domain\ValueObjects\Size;
use App\Domain\ValueObjects\Color;
use App\Domain\ValueObjects\Brand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

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
     * フィルタリングされた洋服一覧を取得
     */
    public function getFilteredClothes(array $filters): array
    {
        $userId = Auth::id();
        $clothes = $this->clothesRepository->findByUserId($userId);

        if (isset($filters['size'])) {
            try {
                $clothes = $this->getClothesBySize($userId, $filters['size']);
            } catch (\InvalidArgumentException $e) {
                // 無効なサイズの場合は無視
            }
        }

        if (isset($filters['brand'])) {
            $clothes = $this->getClothesByBrand($userId, $filters['brand']);
        }

        if (isset($filters['color'])) {
            if ($filters['color'] === 'dark') {
                $clothes = $this->getDarkColoredClothes($userId);
            } elseif ($filters['color'] === 'bright') {
                $clothes = $this->getBrightColoredClothes($userId);
            }
        }

        return [
            'clothes' => $clothes,
            'availableSizes' => Size::getAvailableSizes()
        ];
    }

    /**
     * 作成フォーム用のデータを取得
     */
    public function getCreateFormData(): array
    {
        return [
            'categories' => $this->categoryRepository->getRootCategories(),
            'availableSizes' => Size::getAvailableSizes()
        ];
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
        array $validated,
        ?UploadedFile $image = null
    ): ?Clothes {
        $userId = Auth::id();
        $clothes = $this->clothesRepository->findById($id);
        
        if (!$clothes || $clothes->getUserId() !== $userId) {
            throw new \Exception('洋服が見つかりません。');
        }

        $imagePath = $clothes->getImagePath();
        if ($image) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $image->store('clothes', 'public');
        }

        // 値オブジェクトの作成
        $sizeObj = null;
        if (isset($validated['size'])) {
            try {
                $sizeObj = new Size($validated['size']);
            } catch (\InvalidArgumentException $e) {
                // サイズが無効な場合は無視
            }
        }
        
        $colorObj = null;
        if (isset($validated['color_name']) && isset($validated['color_code'])) {
            try {
                $colorObj = new Color($validated['color_name'], $validated['color_code']);
            } catch (\InvalidArgumentException $e) {
                // 色が無効な場合は無視
            }
        }
        
        $brandObj = null;
        if (isset($validated['brand_name'])) {
            try {
                $brandObj = new Brand(
                    $validated['brand_name'],
                    $validated['brand_description'] ?? null,
                    $validated['brand_country'] ?? null
                );
            } catch (\InvalidArgumentException $e) {
                // ブランドが無効な場合は無視
            }
        }

        $clothes->changeName($validated['name']);
        $clothes->changeDescription($validated['description']);
        $clothes->changeImage($imagePath);
        $clothes->changeCategory($validated['category_id']);
        $clothes->changeSize($sizeObj);
        $clothes->changeColor($colorObj);
        $clothes->changeBrand($brandObj);

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

    /**
     * 洋服詳細を取得
     */
    public function getClothesDetail(int $id): array
    {
        $userId = Auth::id();
        $clothes = $this->clothesRepository->findByUserId($userId);
        
        $targetClothes = null;
        foreach ($clothes as $item) {
            if ($item->getId() == $id) {
                $targetClothes = $item;
                break;
            }
        }

        if (!$targetClothes) {
            throw new \Exception('洋服が見つかりません。');
        }

        return ['clothes' => $targetClothes];
    }

    /**
     * 編集フォーム用のデータを取得
     */
    public function getEditFormData(int $id): array
    {
        $userId = Auth::id();
        $clothes = $this->clothesRepository->findByUserId($userId);
        
        $targetClothes = null;
        foreach ($clothes as $item) {
            if ($item->getId() == $id) {
                $targetClothes = $item;
                break;
            }
        }

        if (!$targetClothes) {
            throw new \Exception('洋服が見つかりません。');
        }

        return [
            'clothes' => $targetClothes,
            'categories' => $this->categoryRepository->getRootCategories(),
            'availableSizes' => Size::getAvailableSizes()
        ];
    }

    /**
     * ダッシュボードデータを取得
     */
    public function getDashboardData(): array
    {
        $userId = Auth::id();
        return [
            'clothes' => collect($this->clothesRepository->findByUserId($userId)),
            'categories' => $this->categoryRepository->getRootCategories()
        ];
    }
} 