<?php

namespace App\Domain\Entities\Interfaces;

interface SoftDeletableInterface
{
    public function isDeleted(): bool;
    public function delete(): void;
    public function restore(): void;
} 