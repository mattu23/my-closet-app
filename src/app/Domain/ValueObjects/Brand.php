<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

class Brand
{
    private string $name;
    private ?string $description;
    private ?string $country;
    
    public function __construct(string $name, ?string $description = null, ?string $country = null)
    {
        $this->name = trim($name);
        
        if (empty($this->name)) {
            throw new InvalidArgumentException("ブランド名を指定してください");
        }
        
        $this->description = $description ? trim($description) : null;
        $this->country = $country ? trim($country) : null;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    public function getCountry(): ?string
    {
        return $this->country;
    }
    
    public function equals(Brand $other): bool
    {
        return $this->name === $other->name;
    }
    
    public function __toString(): string
    {
        if ($this->country) {
            return "{$this->name} ({$this->country})";
        }
        return $this->name;
    }
    
    // ブランドの詳細情報を取得
    public function getFullInfo(): string
    {
        $info = $this->name;
        
        if ($this->country) {
            $info .= " ({$this->country})";
        }
        
        if ($this->description) {
            $info .= ": {$this->description}";
        }
        
        return $info;
    }
    
    // ブランド名が特定のキーワードを含むかどうか
    public function containsKeyword(string $keyword): bool
    {
        return stripos($this->name, $keyword) !== false;
    }
} 