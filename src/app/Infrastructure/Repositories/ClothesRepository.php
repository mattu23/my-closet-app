<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Clothes;
use App\Domain\Repositories\ClothesRepositoryInterface;
use App\Domain\Repositories\CategoryRepositoryInterface;
use App\Models\Clothes as ClothesModel;
use Illuminate\Support\Facades\DB;

class ClothesRepository implements ClothesRepositoryInterface
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?array
    {
        $model = ClothesModel::find($id);
        return $model ? [
            'id' => $model->id,
            'name' => $model->name,
            'description' => $model->description,
            'image_path' => $model->image_path,
            'category_id' => $model->category_id,
            'user_id' => $model->user_id,
            'size' => $model->size,
            'color_name' => $model->color_name,
            'color_code' => $model->color_code,
            'brand_name' => $model->brand_name,
            'brand_description' => $model->brand_description,
            'brand_country' => $model->brand_country,
        ] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function findByUserId(int $userId): array
    {
        $models = ClothesModel::where('user_id', $userId)->get();
        return $models->map(function ($model) {
            return [
                'id' => $model->id,
                'name' => $model->name,
                'description' => $model->description,
                'image_path' => $model->image_path,
                'category_id' => $model->category_id,
                'user_id' => $model->user_id,
                'size' => $model->size,
                'color_name' => $model->color_name,
                'color_code' => $model->color_code,
                'brand_name' => $model->brand_name,
                'brand_description' => $model->brand_description,
                'brand_country' => $model->brand_country,
            ];
        })->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function findByCategoryId(int $categoryId): array
    {
        $models = ClothesModel::where('category_id', $categoryId)->get();
        return $models->map(function ($model) {
            return [
                'id' => $model->id,
                'name' => $model->name,
                'description' => $model->description,
                'image_path' => $model->image_path,
                'category_id' => $model->category_id,
                'user_id' => $model->user_id,
                'size' => $model->size,
                'color_name' => $model->color_name,
                'color_code' => $model->color_code,
                'brand_name' => $model->brand_name,
                'brand_description' => $model->brand_description,
                'brand_country' => $model->brand_country,
            ];
        })->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function findByCategoryAndChildren(int $categoryId): array
    {
        // カテゴリーとその子カテゴリーのIDリストを取得
        $categoryIds = [$categoryId];
        $children = $this->categoryRepository->findAllChildren($categoryId);
        foreach ($children as $child) {
            $categoryIds[] = $child->getId();
        }

        // カテゴリーIDリストに一致する洋服を取得
        $models = ClothesModel::whereIn('category_id', $categoryIds)->get();
        return $models->map(function ($model) {
            return [
                'id' => $model->id,
                'name' => $model->name,
                'description' => $model->description,
                'image_path' => $model->image_path,
                'category_id' => $model->category_id,
                'user_id' => $model->user_id,
                'size' => $model->size,
                'color_name' => $model->color_name,
                'color_code' => $model->color_code,
                'brand_name' => $model->brand_name,
                'brand_description' => $model->brand_description,
                'brand_country' => $model->brand_country,
            ];
        })->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function findByCoordinateId(int $coordinateId): array
    {
        $models = ClothesModel::whereHas('coordinates', function ($query) use ($coordinateId) {
            $query->where('coordinates.id', $coordinateId);
        })->get();

        return $models->map(function ($model) {
            return [
                'id' => $model->id,
                'name' => $model->name,
                'description' => $model->description,
                'image_path' => $model->image_path,
                'category_id' => $model->category_id,
                'user_id' => $model->user_id,
                'size' => $model->size,
                'color_name' => $model->color_name,
                'color_code' => $model->color_code,
                'brand_name' => $model->brand_name,
                'brand_description' => $model->brand_description,
                'brand_country' => $model->brand_country,
            ];
        })->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function save(Clothes $clothes): void
    {
        DB::transaction(function () use ($clothes) {
            $model = ClothesModel::findOrNew($clothes->getId() ?: null);
            $model->name = $clothes->getName();
            $model->description = $clothes->getDescription();
            $model->image_path = $clothes->getImagePath();
            $model->category_id = $clothes->getCategoryId();
            $model->user_id = $clothes->getUserId();
            $model->size = $clothes->getSize();
            $model->color_name = $clothes->getColorName();
            $model->color_code = $clothes->getColorCode();
            $model->brand_name = $clothes->getBrandName();
            $model->brand_description = $clothes->getBrandDescription();
            $model->brand_country = $clothes->getBrandCountry();
            
            if ($clothes->isDeleted()) {
                $model->deleted_at = $clothes->getDeletedAt();
            } else {
                $model->deleted_at = null;
            }
            
            $model->save();
            
            // IDが0（新規作成）の場合、エンティティにIDを設定
            if ($clothes->getId() === 0) {
                $reflectionClass = new \ReflectionClass($clothes);
                $reflectionProperty = $reflectionClass->getProperty('id');
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($clothes, $model->id);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Clothes $clothes): void
    {
        $clothes->delete();
        $this->save($clothes);
    }
} 