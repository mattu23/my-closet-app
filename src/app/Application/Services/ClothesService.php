<?php

namespace App\Application\Services;

use App\Domain\Repositories\ClothesRepositoryInterface;
use App\Domain\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

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
            $clothes = array_filter($clothes, function ($clothes) use ($filters) {
                return $clothes['size'] === $filters['size'];
            });
        }

        if (isset($filters['brand'])) {
            $clothes = array_filter($clothes, function ($clothes) use ($filters) {
                return $clothes['brand_name'] === $filters['brand'];
            });
        }

        return [
            'clothes' => $clothes,
            'availableSizes' => ['S', 'M', 'L', 'XL', 'XXL']
        ];
    }

    /**
     * 作成フォーム用のデータを取得
     */
    public function getCreateFormData(): array
    {
        return [
            'categories' => $this->categoryRepository->getRootCategories(),
            'availableSizes' => ['S', 'M', 'L', 'XL', 'XXL']
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
    ): array {
        $clothes = [
            'id' => 0,
            'name' => $name,
            'description' => $description,
            'image_path' => $imagePath,
            'category_id' => $categoryId,
            'user_id' => $userId,
            'size' => $size,
            'color_name' => $colorData['name'] ?? null,
            'color_code' => $colorData['hex_code'] ?? null,
            'brand_name' => $brandData['name'] ?? null,
            'brand_description' => $brandData['description'] ?? null,
            'brand_country' => $brandData['country'] ?? null,
        ];

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
    ): ?array {
        $userId = Auth::id();
        $clothes = $this->clothesRepository->findById($id);
        
        if (!$clothes || $clothes['user_id'] !== $userId) {
            throw new \Exception('洋服が見つかりません。');
        }

        $imagePath = $clothes['image_path'];
        if ($image) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $image->store('clothes', 'public');
        }

        $clothes['name'] = $validated['name'];
        $clothes['description'] = $validated['description'];
        $clothes['image_path'] = $imagePath;
        $clothes['category_id'] = $validated['category_id'];

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

        $clothes['deleted_at'] = date('Y-m-d H:i:s');
        $this->clothesRepository->save($clothes);
        return true;
    }

    /**
     * ユーザーの洋服一覧を取得
     */
    public function getClothesByUserId(int $userId): array
    {
        return $this->clothesRepository->findByUserId($userId);
    }

    /**
     * カテゴリーに属する洋服一覧を取得
     */
    public function getClothesByCategoryId(int $categoryId): array
    {
        return $this->clothesRepository->findByCategoryId($categoryId);
    }

    /**
     * カテゴリーとその子カテゴリーに属する洋服一覧を取得
     */
    public function getClothesByCategoryAndChildren(int $categoryId): array
    {
        return $this->clothesRepository->findByCategoryAndChildren($categoryId);
    }
    
    /**
     * 特定のサイズの洋服を取得
     */
    public function getClothesBySize(int $userId, string $size): array
    {
        $allClothes = $this->getClothesByUserId($userId);
        return array_filter($allClothes, function ($clothes) use ($size) {
            return $clothes['size'] === $size;
        });
    }
    
    /**
     * 特定のブランドの洋服を取得
     */
    public function getClothesByBrand(int $userId, string $brandName): array
    {
        $allClothes = $this->getClothesByUserId($userId);
        return array_filter($allClothes, function ($clothes) use ($brandName) {
            return $clothes['brand_name'] === $brandName;
        });
    }

    /**
     * 洋服詳細を取得
     */
    public function getClothesDetail(int $id): array
    {
        $userId = Auth::id();
        $clothes = $this->clothesRepository->findById($id);
        
        if (!$clothes || $clothes['user_id'] !== $userId) {
            throw new \Exception('洋服が見つかりません。');
        }

        return ['clothes' => $clothes];
    }

    /**
     * 編集フォーム用のデータを取得
     */
    public function getEditFormData(int $id): array
    {
        $userId = Auth::id();
        $clothes = $this->clothesRepository->findById($id);
        
        if (!$clothes || $clothes['user_id'] !== $userId) {
            throw new \Exception('洋服が見つかりません。');
        }

        return [
            'clothes' => $clothes,
            'categories' => $this->categoryRepository->getRootCategories(),
            'availableSizes' => ['S', 'M', 'L', 'XL', 'XXL']
        ];
    }

    /**
     * ダッシュボードデータを取得
     */
    public function getDashboardData(): array
    {
        $userId = Auth::id();
        Log::info('userIdの取得成功'. $userId);

        if (!$userId) {
            throw new \Exception('ユーザーが見つかりません。');
        }

        return [
            'clothes' => collect($this->clothesRepository->findByUserId($userId)),
            'categories' => $this->categoryRepository->getRootCategories()
        ];
    }
} 