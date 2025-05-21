<?php

namespace App\Domain\Entities;

use App\Domain\Entities\Interfaces\EntityInterface;
use App\Domain\Entities\Interfaces\SoftDeletableInterface;

class Clothes implements EntityInterface, SoftDeletableInterface
{
    private int $id;
    private string $name;
    private ?string $description;
    private ?string $imagePath;
    private int $categoryId;
    private int $userId;
    private ?string $createdAt;
    private ?string $updatedAt;
    private ?string $deletedAt;
    private ?string $size = null;
    private ?string $colorName = null;
    private ?string $colorCode = null;
    private ?string $brandName = null;
    private ?string $brandDescription = null;
    private ?string $brandCountry = null;

    public function __construct(
        int $id,
        string $name,
        ?string $description,
        ?string $imagePath,
        int $categoryId,
        int $userId,
        ?string $size = null,
        ?string $colorName = null,
        ?string $colorCode = null,
        ?string $brandName = null,
        ?string $brandDescription = null,
        ?string $brandCountry = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->imagePath = $imagePath;
        $this->categoryId = $categoryId;
        $this->userId = $userId;
        $this->size = $size;
        $this->colorName = $colorName;
        $this->colorCode = $colorCode;
        $this->brandName = $brandName;
        $this->brandDescription = $brandDescription;
        $this->brandCountry = $brandCountry;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function getColorName(): ?string
    {
        return $this->colorName;
    }

    public function getColorCode(): ?string
    {
        return $this->colorCode;
    }

    public function getBrandName(): ?string
    {
        return $this->brandName;
    }

    public function getBrandDescription(): ?string
    {
        return $this->brandDescription;
    }

    public function getBrandCountry(): ?string
    {
        return $this->brandCountry;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function delete(): void
    {
        $this->deletedAt = date('Y-m-d H:i:s');
    }

    public function restore(): void
    {
        $this->deletedAt = null;
    }

    public function getDeletedAt(): ?string
    {
        return $this->deletedAt;
    }

    public function changeName(string $name): void
    {
        $this->name = $name;
    }

    public function changeDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function changeImage(?string $imagePath): void
    {
        $this->imagePath = $imagePath;
    }

    public function changeCategory(int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

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
            'color_code' => $this->colorCode,
            'brand_name' => $this->brandName,
            'brand_description' => $this->brandDescription,
            'brand_country' => $this->brandCountry,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'deleted_at' => $this->deletedAt,
        ];
    }
} 