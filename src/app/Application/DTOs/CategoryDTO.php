<?php

namespace App\Application\DTOs;

use App\Domain\Entities\Category;

class CategoryDTO
{
    public function __construct(
        private string $name,
        private ?string $description,
        private ?int $parentId,
        private ?int $id = null
    ) {
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

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * ドメインエンティティからDTOを作成
     */
    public static function fromEntity(Category $category): self
    {
        return new self(
            $category->getName(),
            $category->getDescription(),
            $category->getParentId(),
            $category->getId()
        );
    }

    /**
     * DTOからドメインエンティティを作成
     */
    public function toEntity(): Category
    {
        return new Category(
            $this->id ?? 0,
            $this->name,
            $this->description,
            $this->parentId
        );
    }

    /**
     * 配列からDTOを作成
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['description'] ?? null,
            $data['parent_id'] ?? null,
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
            'parent_id' => $this->parentId,
            'description' => $this->description,
        ];
    }
} 