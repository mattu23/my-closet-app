<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Coordinate;
use App\Domain\Repositories\CoordinateRepositoryInterface;
use App\Models\Coordinate as CoordinateModel;
use Illuminate\Support\Facades\DB;

class CoordinateRepository implements CoordinateRepositoryInterface
{
    /**
     * モデルからエンティティに変換
     */
    private function toEntity(CoordinateModel $model): Coordinate
    {
        // 洋服IDのリストを取得
        $clothesIds = $model->clothes->pluck('id')->toArray();
        
        return new Coordinate(
            $model->id,
            $model->name,
            $model->description,
            $model->image_path,
            $clothesIds,
            $model->user_id,
            $model->deleted_at ? $model->deleted_at->format('Y-m-d H:i:s') : null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?Coordinate
    {
        $model = CoordinateModel::with('clothes')->find($id);
        return $model ? $this->toEntity($model) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function findByUserId(int $userId): array
    {
        $models = CoordinateModel::with('clothes')->where('user_id', $userId)->get();
        return $models->map(function ($model) {
            return $this->toEntity($model);
        })->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function findByClothesId(int $clothesId): array
    {
        $models = CoordinateModel::with('clothes')
            ->whereHas('clothes', function ($query) use ($clothesId) {
                $query->where('clothes.id', $clothesId);
            })
            ->get();
        
        return $models->map(function ($model) {
            return $this->toEntity($model);
        })->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function save(Coordinate $coordinate): void
    {
        DB::transaction(function () use ($coordinate) {
            $model = CoordinateModel::findOrNew($coordinate->getId() ?: null);
            $model->name = $coordinate->getName();
            $model->description = $coordinate->getDescription();
            $model->image_path = $coordinate->getImagePath();
            $model->user_id = $coordinate->getUserId();
            
            if ($coordinate->isDeleted()) {
                $model->deleted_at = $coordinate->getDeletedAt();
            } else {
                $model->deleted_at = null;
            }
            
            $model->save();
            
            // IDが0（新規作成）の場合、エンティティにIDを設定
            if ($coordinate->getId() === 0) {
                $reflectionClass = new \ReflectionClass($coordinate);
                $reflectionProperty = $reflectionClass->getProperty('id');
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($coordinate, $model->id);
            }
            
            // 洋服との関連付けを保存
            $this->saveClothesRelation($model->id, $coordinate->getClothesIds());
        });
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Coordinate $coordinate): void
    {
        $coordinate->delete();
        $this->save($coordinate);
    }

    /**
     * {@inheritdoc}
     */
    public function saveClothesRelation(int $coordinateId, array $clothesIds): void
    {
        $model = CoordinateModel::find($coordinateId);
        if ($model) {
            $model->clothes()->sync($clothesIds);
        }
    }
} 