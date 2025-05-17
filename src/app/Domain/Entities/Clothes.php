<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Size;
use App\Domain\ValueObjects\Color;
use App\Domain\ValueObjects\Brand;

class Clothes
{
    private int $id;
    private string $name;
    private ?string $description;
    private ?string $imagePath;
    private int $categoryId;
    private int $userId;
    private ?string $createdAt;
    private ?string $deletedAt;
    
    // 値オブジェクト
    private ?Size $size;
    private ?Color $color;
    private ?Brand $brand;

    public function __construct(
        int $id,
        string $name,
        ?string $description,
        ?string $imagePath,
        int $categoryId,
        int $userId,
        ?Size $size = null,
        ?Color $color = null,
        ?Brand $brand = null,
        ?string $createdAt = null,
        ?string $deletedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->imagePath = $imagePath;
        $this->categoryId = $categoryId;
        $this->userId = $userId;
        $this->size = $size;
        $this->color = $color;
        $this->brand = $brand;
        $this->createdAt = $createdAt;
        $this->deletedAt = $deletedAt;
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

    public function getSize(): ?Size
    {
        return $this->size;
    }

    public function getColor(): ?Color
    {
        return $this->color;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getDeletedAt(): ?string
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function changeName(string $name): void
    {
        $this->name = $name;
    }

    public function changeDescription(string $description): void
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

    public function changeSize(?Size $size): void
    {
        $this->size = $size;
    }

    public function changeColor(?Color $color): void
    {
        $this->color = $color;
    }

    public function changeBrand(?Brand $brand): void
    {
        $this->brand = $brand;
    }

    public function delete(): void
    {
        $this->deletedAt = date('Y-m-d H:i:s');
    }

    public function restore(): void
    {
        $this->deletedAt = null;
    }
    
    // サイズが指定されているかどうか
    public function hasSize(): bool
    {
        return $this->size !== null;
    }
    
    // 色が指定されているかどうか
    public function hasColor(): bool
    {
        return $this->color !== null;
    }
    
    // ブランドが指定されているかどうか
    public function hasBrand(): bool
    {
        return $this->brand !== null;
    }
    
    // 洋服の詳細情報を取得
    public function getFullInfo(): string
    {
        $info = $this->name;
        
        if ($this->hasBrand()) {
            $info .= " by " . $this->brand->getName();
        }
        
        if ($this->hasSize()) {
            $info .= ", サイズ: " . $this->size->getValue();
        }
        
        if ($this->hasColor()) {
            $info .= ", 色: " . $this->color->getName();
        }
        
        return $info;
    }
} 