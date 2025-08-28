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
            ];
        })->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $clothes): void
    {
        DB::transaction(function () use ($clothes) {
            $model = ClothesModel::findOrNew($clothes['id'] ?: null);
            $model->name = $clothes['name'];
            $model->description = $clothes['description'];
            $model->image_path = $clothes['image_path'];
            $model->category_id = $clothes['category_id'];
            $model->user_id = $clothes['user_id'];
            $model->size = $clothes['size'];
            
            if (isset($clothes['deleted_at'])) {
                $model->deleted_at = $clothes['deleted_at'];
            } else {
                $model->deleted_at = null;
            }
            
            $model->save();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function delete(array $clothes): void
    {
        $clothes['deleted_at'] = date('Y-m-d H:i:s');
        $this->save($clothes);
    }
} 