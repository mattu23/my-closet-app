<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

class Color
{
    private string $name;
    private string $hexCode;
    
    public function __construct(string $name, string $hexCode)
    {
        $this->name = trim($name);
        
        if (empty($this->name)) {
            throw new InvalidArgumentException("色名を指定してください");
        }
        
        // HEXコードのバリデーション（#で始まる6桁の16進数）
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $hexCode)) {
            throw new InvalidArgumentException("無効なHEXカラーコードです: {$hexCode}");
        }
        
        $this->hexCode = $hexCode;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getHexCode(): string
    {
        return $this->hexCode;
    }
    
    public function equals(Color $other): bool
    {
        return $this->hexCode === $other->hexCode;
    }
    
    public function __toString(): string
    {
        return $this->name;
    }
    
    // 色の明るさを判定（0-255）
    public function getBrightness(): int
    {
        $hex = ltrim($this->hexCode, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // 明るさの計算式（人間の目は緑に最も敏感）
        return (int)(($r * 299 + $g * 587 + $b * 114) / 1000);
    }
    
    // 暗い色かどうか判定
    public function isDark(): bool
    {
        return $this->getBrightness() < 128;
    }
    
    // 明るい色かどうか判定
    public function isBright(): bool
    {
        return $this->getBrightness() >= 128;
    }
    
    // 補色を取得
    public function getComplementaryColor(): Color
    {
        $hex = ltrim($this->hexCode, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        $r = 255 - $r;
        $g = 255 - $g;
        $b = 255 - $b;
        
        $complementaryHex = sprintf("#%02x%02x%02x", $r, $g, $b);
        return new Color("Complementary of {$this->name}", $complementaryHex);
    }
} 