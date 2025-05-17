<?php

namespace App\Domain\Entities;

class User
{
    private int $id;
    private string $name;
    private string $email;
    private string $password;
    private ?string $deletedAt;

    public function __construct(
        int $id,
        string $name,
        string $email,
        string $password,
        ?string $deletedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
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

    public function changeEmail(string $email): void
    {
        $this->email = $email;
    }

    public function changePassword(string $password): void
    {
        $this->password = $password;
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