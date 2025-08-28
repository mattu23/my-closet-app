<?php

namespace App\Domain\Entities;

class Category
{
    public function __construct(
        private int $id,
        private string $name,
        private ?string $description,
        private ?int $parentId,
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
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

    public function isRoot(): bool
    {
        return $this->parentId === null;
    }
} 