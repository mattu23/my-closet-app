<?php

namespace App\Application\DTOs;

use App\Domain\ValueObjects\Brand;
use App\Domain\ValueObjects\Color;
use App\Domain\ValueObjects\Size;

class ClothesDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $description,
        public readonly ?string $imagePath,
        public readonly int $categoryId,
        public readonly int $userId,
        public readonly ?string $size,
        public readonly ?string $colorName,
        public readonly ?string $colorHex,
        public readonly ?string $brandName,
        public readonly ?string $deletedAt = null,
    ) {
    }

    /**
     * ドメインエンティティからDTOを作成
     */
    public static function fromEntity(\App\Domain\Entities\Clothes $clothes): self
    {
        return new self(
            id: $clothes->getId(),
            name: $clothes->getName(),
            description: $clothes->getDescription(),
            imagePath: $clothes->getImagePath(),
            categoryId: $clothes->getCategoryId(),
            userId: $clothes->getUserId(),
            size: $clothes->hasSize() ? $clothes->getSize()->getValue() : null,
            colorName: $clothes->hasColor() ? $clothes->getColor()->getName() : null,
            colorHex: $clothes->hasColor() ? $clothes->getColor()->getHexCode() : null,
            brandName: $clothes->hasBrand() ? $clothes->getBrand()->getName() : null,
            deletedAt: $clothes->getDeletedAt(),
        );
    }

    /**
     * DTOからドメインエンティティを作成
     */
    public function toEntity(): \App\Domain\Entities\Clothes
    {
        return new \App\Domain\Entities\Clothes(
            id: $this->id,
            name: $this->name,
            description: $this->description,
            imagePath: $this->imagePath,
            categoryId: $this->categoryId,
            userId: $this->userId,
            size: $this->size ? new Size($this->size) : null,
            color: ($this->colorName && $this->colorHex) ? new Color($this->colorName, $this->colorHex) : null,
            brand: $this->brandName ? new Brand($this->brandName) : null,
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
            categoryId: $data['category_id'],
            userId: $data['user_id'],
            size: $data['size'] ?? null,
            colorName: $data['color_name'] ?? null,
            colorHex: $data['color_hex'] ?? null,
            brandName: $data['brand_name'] ?? null,
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
            'category_id' => $this->categoryId,
            'user_id' => $this->userId,
            'size' => $this->size,
            'color_name' => $this->colorName,
            'color_hex' => $this->colorHex,
            'brand_name' => $this->brandName,
            'deleted_at' => $this->deletedAt,
        ];
    }
} 