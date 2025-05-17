<?php

namespace App\Application\Services;

use App\Application\DTOs\CategoryDTO;
use App\Domain\Entities\Category;
use App\Domain\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {
    }

    /**
     * カテゴリーを作成
     */
    public function createCategory(CategoryDTO $dto): CategoryDTO
    {
        $category = $this->categoryRepository->create($dto->toEntity());
        return CategoryDTO::fromEntity($category);
    }

    /**
     * カテゴリーを更新
     */
    public function updateCategory(int $id, CategoryDTO $dto): ?CategoryDTO
    {
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            return null;
        }

        $updatedCategory = $this->categoryRepository->update($id, $dto->toEntity());
        return CategoryDTO::fromEntity($updatedCategory);
    }

    /**
     * カテゴリーを取得
     */
    public function getCategory(int $id): ?CategoryDTO
    {
        $category = $this->categoryRepository->findById($id);
        return $category ? CategoryDTO::fromEntity($category) : null;
    }

    /**
     * ルートカテゴリーを取得
     */
    public function getRootCategories(): array
    {
        $categories = $this->categoryRepository->findRootCategories();
        return array_map(fn($category) => CategoryDTO::fromEntity($category), $categories);
    }

    /**
     * 子カテゴリーを取得
     */
    public function getChildCategories(int $parentId): array
    {
        $categories = $this->categoryRepository->findByParentId($parentId);
        return array_map(fn($category) => CategoryDTO::fromEntity($category), $categories);
    }
} 