<?php

namespace App\Application\DTOs;

class CoordinateDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $description,
        public readonly ?string $imagePath,
        public readonly array $clothesIds,
        public readonly int $userId,
        public readonly ?string $deletedAt = null,
    ) {
    }

    /**
     * ドメインエンティティからDTOを作成
     */
    public static function fromEntity(\App\Domain\Entities\Coordinate $coordinate): self
    {
        return new self(
            id: $coordinate->getId(),
            name: $coordinate->getName(),
            description: $coordinate->getDescription(),
            imagePath: $coordinate->getImagePath(),
            clothesIds: $coordinate->getClothesIds(),
            userId: $coordinate->getUserId(),
            deletedAt: $coordinate->getDeletedAt(),
        );
    }

    /**
     * DTOからドメインエンティティを作成
     */
    public function toEntity(): \App\Domain\Entities\Coordinate
    {
        return new \App\Domain\Entities\Coordinate(
            id: $this->id,
            name: $this->name,
            description: $this->description,
            imagePath: $this->imagePath,
            clothesIds: $this->clothesIds,
            userId: $this->userId,
            deletedAt: $this->deletedAt,
        );
    }

    /**
     * 配列からDTOを作成
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
            description: $data['description'] ?? '',
            imagePath: $data['image_path'] ?? null,
            clothesIds: $data['clothes_ids'] ?? [],
            userId: $data['user_id'],
            deletedAt: $data['deleted_at'] ?? null,
        );
    }

    /**
     * DTOを配列に変換
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image_path' => $this->imagePath,
            'clothes_ids' => $this->clothesIds,
            'user_id' => $this->userId,
            'deleted_at' => $this->deletedAt,
        ];
    }
} 