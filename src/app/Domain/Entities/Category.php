<?php

namespace App\Domain\Entities;

class Category
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $slug,
        private readonly ?string $description,
        private readonly ?int $parentId,
        private readonly ?string $path,
        private readonly int $level
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function isRoot(): bool
    {
        return $this->parentId === null;
    }

    public function hasChildren(): bool
    {
        return $this->level > 1;
    }
} 