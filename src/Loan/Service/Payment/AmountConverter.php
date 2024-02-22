<?php

declare(strict_types=1);

namespace App\Loan\Service\Payment;

final readonly class AmountConverter
{
    /**
     * @throws \InvalidArgumentException
     */
    public static function toInt(string $amount): int
    {
        $splitAmount = explode('.', $amount);

        $first = $splitAmount[0] ?? null;
        $last = $splitAmount[1] ?? null;

        if (false === is_numeric($first) || false === is_numeric($last)) {
            throw new \InvalidArgumentException(sprintf('invalid amount provided: "%s"', $amount));
        }

        return (int) $first * 100 + (int) $last;
    }

    public static function toString(int $amount): string
    {
        $amount = $amount / 100;

        return number_format($amount, 2);
    }
}
