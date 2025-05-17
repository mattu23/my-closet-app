<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Category;
use App\Domain\Repositories\CategoryRepositoryInterface;
use App\Models\Category as CategoryModel;
use Illuminate\Support\Facades\DB;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * モデルからエンティティに変換
     */
    private function toEntity(CategoryModel $model): Category
    {
        return new Category(
            $model->id,
            $model->name,
            $model->slug,
            $model->description,
            $model->parent_id,
            $model->path,
            $model->level
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?Category
    {
        $model = CategoryModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Category $category): Category
    {
        $model = new CategoryModel();
        $model->name = $category->getName();
        $model->slug = $category->getSlug();
        $model->description = $category->getDescription();
        $model->parent_id = $category->getParentId();
        $model->path = $category->getPath();
        $model->level = $category->getLevel();
        $model->save();

        return $this->toEntity($model);
    }

    /**
     * {@inheritdoc}
     */
    public function update(int $id, Category $category): Category
    {
        $model = CategoryModel::findOrFail($id);
        $model->name = $category->getName();
        $model->slug = $category->getSlug();
        $model->description = $category->getDescription();
        $model->parent_id = $category->getParentId();
        $model->path = $category->getPath();
        $model->level = $category->getLevel();
        $model->save();

        return $this->toEntity($model);
    }

    /**
     * {@inheritdoc}
     */
    public function findRootCategories(): array
    {
        return CategoryModel::whereNull('parent_id')
            ->get()
            ->map(fn($model) => $this->toEntity($model))
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function findByParentId(int $parentId): array
    {
        return CategoryModel::where('parent_id', $parentId)
            ->get()
            ->map(fn($model) => $this->toEntity($model))
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function findAllChildren(int $categoryId): array
    {
        $result = [];
        $this->findChildrenRecursive($categoryId, $result);
        return $result;
    }

    /**
     * 子カテゴリーを再帰的に検索
     */
    private function findChildrenRecursive(int $parentId, array &$result): void
    {
        $children = $this->findByParentId($parentId);
        foreach ($children as $child) {
            $result[] = $child;
            $this->findChildrenRecursive($child->getId(), $result);
        }
    }
} 