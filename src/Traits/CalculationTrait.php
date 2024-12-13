<?php

declare(strict_types=1);

namespace App\Traits;

trait CalculationTrait
{
    /**
     * @return string
     */
    protected function generateId(): string
    {
        $firstDigit = random_int(1, 9);
        $remainingDigits = random_int(0, 9999);

        return $firstDigit . str_pad((string)$remainingDigits, 4, '0', STR_PAD_LEFT);
    }

    /**
     * @return float
     */
    protected function generateUniBonusAmount(): float
    {
        $min = 2.3;
        $max = 4.2;

        $scale = 10;
        $random = random_int((int)($min * $scale), (int)($max * $scale));

        return $random / $scale;
    }
}