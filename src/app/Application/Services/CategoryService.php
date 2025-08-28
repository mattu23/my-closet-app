<?php

namespace App\Application\Services;

use App\Application\DTOs\CategoryDTO;
use App\Domain\Entities\Category;
use App\Domain\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {
    }

    /**
     * カテゴリーを作成
     */
    public function createCategory(array $validated): CategoryDTO
    {
        $dto = new CategoryDTO(
            $validated['name'],
            $validated['description'] ?? null,
            $validated['parent_id'] ?? null
        );

        $category = $this->categoryRepository->create($dto->toEntity());
        return CategoryDTO::fromEntity($category);
    }

    /**
     * カテゴリーを更新
     */
    public function updateCategory(int $id, array $validated): ?CategoryDTO
    {
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            return null;
        }

        $dto = new CategoryDTO(
            $validated['name'],
            $validated['description'] ?? null,
            $validated['parent_id'] ?? null
        );

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
        return $this->categoryRepository->getRootCategories();
    }

    /**
     * 子カテゴリーを取得
     */
    public function getChildCategories(int $parentId): array
    {
        $categories = $this->categoryRepository->findByParentId($parentId);
        return array_map(fn($category) => CategoryDTO::fromEntity($category), $categories);
    }

    /**
     * 作成フォーム用のデータを取得
     */
    public function getCreateFormData(): array
    {
        return [
            'categories' => $this->categoryRepository->getRootCategories()
        ];
    }

    /**
     * カテゴリーデータを取得
     */
    public function getCategoryData(int $id): array
    {
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            throw new \Exception('カテゴリーが見つかりません。');
        }

        return [
            'category' => CategoryDTO::fromEntity($category),
            'children' => array_map(fn($child) => CategoryDTO::fromEntity($child), $this->categoryRepository->getChildren($id))
        ];
    }

    /**
     * 編集フォーム用のデータを取得
     */
    public function getEditFormData(int $id): array
    {
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            throw new \Exception('カテゴリーが見つかりません。');
        }

        return [
            'category' => CategoryDTO::fromEntity($category),
            'categories' => $this->categoryRepository->getRootCategories()
        ];
    }

    /**
     * カテゴリーを削除
     */
    public function deleteCategory(int $id): void
    {
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            throw new \Exception('カテゴリーが見つかりません。');
        }

        $this->categoryRepository->delete($id);
    }

    /**
     * 子カテゴリーデータを取得
     */
    public function getChildrenData(int $id): array
    {
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            throw new \Exception('カテゴリーが見つかりません。');
        }

        return [
            'category' => CategoryDTO::fromEntity($category),
            'children' => array_map(fn($child) => CategoryDTO::fromEntity($child), $this->categoryRepository->getChildren($id))
        ];
    }
} 