<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

class Size
{
    private string $value;
    private static array $sizeOrder = [
        "XS" => 1, 
        "S" => 2, 
        "M" => 3, 
        "L" => 4, 
        "XL" => 5, 
        "XXL" => 6
    ];
    
    public function __construct(string $value)
    {
        $value = strtoupper(trim($value));
        if (!array_key_exists($value, self::$sizeOrder)) {
            throw new InvalidArgumentException("無効なサイズです: {$value}");
        }
        $this->value = $value;
    }
    
    public function getValue(): string
    {
        return $this->value;
    }
    
    public function isLargerThan(Size $other): bool
    {
        return self::$sizeOrder[$this->value] > self::$sizeOrder[$other->value];
    }
    
    public function isSmallerThan(Size $other): bool
    {
        return self::$sizeOrder[$this->value] < self::$sizeOrder[$other->value];
    }
    
    public function equals(Size $other): bool
    {
        return $this->value === $other->value;
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
    
    // 全サイズリストを取得
    public static function getAvailableSizes(): array
    {
        return array_keys(self::$sizeOrder);
    }
    
    // サイズの序数を取得（比較用）
    public function getOrder(): int
    {
        return self::$sizeOrder[$this->value];
    }
} 