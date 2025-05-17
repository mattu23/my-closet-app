<?php

namespace App\Domain\Entities;

class Coordinate
{
    /**
     * @param array<int> $clothesIds
     */
    public function __construct(
        private int $id,
        private string $name,
        private string $description,
        private ?string $imagePath,
        private array $clothesIds,
        private int $userId,
        private ?string $createdAt = null,
        private ?string $deletedAt = null
    ) {
    }

    public function getId(): int
    {
        return $this->id;
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

    /**
     * @return array<int>
     */
    public function getClothesIds(): array
    {
        return $this->clothesIds;
    }

    public function getUserId(): int
    {
        return $this->userId;
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

    public function hasClothes(int $clothesId): bool
    {
        return in_array($clothesId, $this->clothesIds, true);
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

    public function addClothes(int $clothesId): void
    {
        if (!in_array($clothesId, $this->clothesIds)) {
            $this->clothesIds[] = $clothesId;
        }
    }

    public function removeClothes(int $clothesId): void
    {
        $key = array_search($clothesId, $this->clothesIds);
        if ($key !== false) {
            unset($this->clothesIds[$key]);
            $this->clothesIds = array_values($this->clothesIds);
        }
    }

    public function setClothes(array $clothesIds): void
    {
        $this->clothesIds = $clothesIds;
    }

    public function delete(): void
    {
        $this->deletedAt = date('Y-m-d H:i:s');
    }

    public function restore(): void
    {
        $this->deletedAt = null;
    }
} 