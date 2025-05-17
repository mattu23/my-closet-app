<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Clothes;
use App\Domain\Repositories\ClothesRepositoryInterface;
use App\Domain\Repositories\CategoryRepositoryInterface;
use App\Domain\ValueObjects\Size;
use App\Domain\ValueObjects\Color;
use App\Domain\ValueObjects\Brand;
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
     * モデルからエンティティに変換
     */
    private function toEntity(ClothesModel $model): Clothes
    {
        // 値オブジェクトの作成
        $size = $model->size ? new Size($model->size) : null;
        
        $color = null;
        if ($model->color_name && $model->color_code) {
            try {
                $color = new Color($model->color_name, $model->color_code);
            } catch (\InvalidArgumentException $e) {
                // 無効な色情報の場合はnullのまま
            }
        }
        
        $brand = null;
        if ($model->brand_name) {
            try {
                $brand = new Brand(
                    $model->brand_name,
                    $model->brand_description,
                    $model->brand_country
                );
            } catch (\InvalidArgumentException $e) {
                // 無効なブランド情報の場合はnullのまま
            }
        }
        
        return new Clothes(
            $model->id,
            $model->name,
            $model->description,
            $model->image_path,
            $model->category_id,
            $model->user_id,
            $size,
            $color,
            $brand,
            $model->deleted_at ? $model->deleted_at->format('Y-m-d H:i:s') : null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?Clothes
    {
        $model = ClothesModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function findByUserId(int $userId): array
    {
        $models = ClothesModel::where('user_id', $userId)->get();
        return $models->map(function ($model) {
            return $this->toEntity($model);
        })->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function findByCategoryId(int $categoryId): array
    {
        $models = ClothesModel::where('category_id', $categoryId)->get();
        return $models->map(function ($model) {
            return $this->toEntity($model);
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
            return $this->toEntity($model);
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
            
            // 値オブジェクトの保存
            if ($clothes->hasSize()) {
                $model->size = $clothes->getSize()->getValue();
            } else {
                $model->size = null;
            }
            
            if ($clothes->hasColor()) {
                $model->color_name = $clothes->getColor()->getName();
                $model->color_code = $clothes->getColor()->getHexCode();
            } else {
                $model->color_name = null;
                $model->color_code = null;
            }
            
            if ($clothes->hasBrand()) {
                $model->brand_name = $clothes->getBrand()->getName();
                $model->brand_description = $clothes->getBrand()->getDescription();
                $model->brand_country = $clothes->getBrand()->getCountry();
            } else {
                $model->brand_name = null;
                $model->brand_description = null;
                $model->brand_country = null;
            }
            
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