<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Coordinate;
use App\Domain\Repositories\CoordinateRepositoryInterface;
use App\Application\DTOs\CoordinateDTO;
use Illuminate\Support\Facades\DB;

class CoordinateRepository implements CoordinateRepositoryInterface
{
    /**
     * コーディネートを作成
     */
    public function create(CoordinateDTO $dto): Coordinate
    {
        $id = DB::table('coordinates')->insertGetId([
            'name' => $dto->getName(),
            'description' => $dto->getDescription(),
            'image_path' => $dto->getImagePath(),
            'user_id' => $dto->getUserId(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $coordinate = $this->findById($id);
        if ($coordinate && !empty($dto->getClothesIds())) {
            $this->saveClothesRelation($id, $dto->getClothesIds());
        }

        return $coordinate;
    }

    /**
     * コーディネートを更新
     */
    public function update(int $id, CoordinateDTO $dto): Coordinate
    {
        DB::table('coordinates')
            ->where('id', $id)
            ->update([
                'name' => $dto->getName(),
                'description' => $dto->getDescription(),
                'image_path' => $dto->getImagePath(),
                'updated_at' => now()
            ]);

        if (!empty($dto->getClothesIds())) {
            $this->saveClothesRelation($id, $dto->getClothesIds());
        }

        return $this->findById($id);
    }

    /**
     * コーディネートを削除
     */
    public function delete(int $id): void
    {
        DB::table('coordinates')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);
    }

    /**
     * IDでコーディネートを取得
     */
    public function findById(int $id): ?Coordinate
    {
        $data = DB::table('coordinates')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$data) {
            return null;
        }

        $clothesIds = DB::table('coordinate_clothes')
            ->where('coordinate_id', $id)
            ->pluck('clothes_id')
            ->toArray();

        return new Coordinate(
            $data->id,
            $data->name,
            $data->description,
            $data->image_path,
            $clothesIds,
            $data->user_id,
            $data->created_at,
            $data->deleted_at
        );
    }

    /**
     * ユーザーIDでコーディネートを取得
     */
    public function findByUserId(int $userId): array
    {
        $data = DB::table('coordinates')
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->get();

        return $data->map(function ($item) {
            $clothesIds = DB::table('coordinate_clothes')
                ->where('coordinate_id', $item->id)
                ->pluck('clothes_id')
                ->toArray();

            return new Coordinate(
                $item->id,
                $item->name,
                $item->description,
                $item->image_path,
                $clothesIds,
                $item->user_id,
                $item->created_at,
                $item->deleted_at
            );
        })->all();
    }

    /**
     * コーディネートに洋服を追加
     */
    public function addClothes(int $coordinateId, int $clothesId): void
    {
        DB::table('coordinate_clothes')->insert([
            'coordinate_id' => $coordinateId,
            'clothes_id' => $clothesId,
            'created_at' => now()
        ]);
    }

    /**
     * コーディネートから洋服を削除
     */
    public function removeClothes(int $coordinateId, int $clothesId): void
    {
        DB::table('coordinate_clothes')
            ->where('coordinate_id', $coordinateId)
            ->where('clothes_id', $clothesId)
            ->delete();
    }

    /**
     * 特定の洋服を含むコーディネートを取得
     */
    public function findByClothesId(int $clothesId): array
    {
        $data = DB::table('coordinates')
            ->join('coordinate_clothes', 'coordinates.id', '=', 'coordinate_clothes.coordinate_id')
            ->where('coordinate_clothes.clothes_id', $clothesId)
            ->whereNull('coordinates.deleted_at')
            ->select('coordinates.*')
            ->get();

        return $data->map(function ($item) {
            $clothesIds = DB::table('coordinate_clothes')
                ->where('coordinate_id', $item->id)
                ->pluck('clothes_id')
                ->toArray();

            return new Coordinate(
                $item->id,
                $item->name,
                $item->description,
                $item->image_path,
                $clothesIds,
                $item->user_id,
                $item->created_at,
                $item->deleted_at
            );
        })->all();
    }

    /**
     * コーディネートを保存
     */
    public function save(Coordinate $coordinate): void
    {
        if ($coordinate->getId()) {
            $this->update($coordinate->getId(), CoordinateDTO::fromEntity($coordinate));
        } else {
            $this->create(CoordinateDTO::fromEntity($coordinate));
        }
    }

    /**
     * コーディネートと洋服の関連付けを保存
     */
    public function saveClothesRelation(int $coordinateId, array $clothesIds): void
    {
        // 既存の関連付けを削除
        DB::table('coordinate_clothes')
            ->where('coordinate_id', $coordinateId)
            ->delete();

        // 新しい関連付けを作成
        $now = now();
        $relations = array_map(function ($clothesId) use ($coordinateId, $now) {
            return [
                'coordinate_id' => $coordinateId,
                'clothes_id' => $clothesId,
                'created_at' => $now
            ];
        }, $clothesIds);

        DB::table('coordinate_clothes')->insert($relations);
    }
} 