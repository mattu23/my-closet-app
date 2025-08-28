<?php

namespace App\Application\DTOs;

use App\Domain\Entities\Coordinate;

class CoordinateDTO
{
    public function __construct(
        private string $name,
        private string $description,
        private ?string $imagePath,
        private int $userId,
        private array $clothesIds,
        private ?int $id = null
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getClothesIds(): array
    {
        return $this->clothesIds;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * ドメインエンティティからDTOを作成
     */
    public static function fromEntity(Coordinate $coordinate): self
    {
        return new self(
            $coordinate->getName(),
            $coordinate->getDescription(),
            $coordinate->getImagePath(),
            $coordinate->getUserId(),
            $coordinate->getClothesIds(),
            $coordinate->getId()
        );
    }

    /**
     * DTOからドメインエンティティを作成
     */
    public function toEntity(): Coordinate
    {
        return new Coordinate(
            $this->id ?? 0,
            $this->name,
            $this->description,
            $this->imagePath,
            $this->clothesIds,
            $this->userId
        );
    }

    /**
     * 配列からDTOを作成
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['description'] ?? '',
            $data['image_path'] ?? null,
            $data['user_id'],
            $data['clothes_ids'] ?? [],
            $data['id'] ?? null
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
        ];
    }
} 