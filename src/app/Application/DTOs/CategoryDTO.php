<?php

namespace App\Application\DTOs;

class CategoryDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?int $parentId,
        public readonly int $userId,
        public readonly ?string $deletedAt = null,
    ) {
    }

    /**
     * ドメインエンティティからDTOを作成
     */
    public static function fromEntity(\App\Domain\Entities\Category $category): self
    {
        return new self(
            id: $category->getId(),
            name: $category->getName(),
            parentId: $category->getParentId(),
            userId: $category->getUserId(),
            deletedAt: $category->getDeletedAt(),
        );
    }

    /**
     * DTOからドメインエンティティを作成
     */
    public function toEntity(): \App\Domain\Entities\Category
    {
        return new \App\Domain\Entities\Category(
            id: $this->id,
            name: $this->name,
            parentId: $this->parentId,
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
            parentId: $data['parent_id'] ?? null,
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
            'parent_id' => $this->parentId,
            'user_id' => $this->userId,
            'deleted_at' => $this->deletedAt,
        ];
    }
} 