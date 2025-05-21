<?php

namespace App\Domain\Entities\Interfaces;

interface EntityInterface
{
    public function getId(): int;
    public function toArray(): array;
} 