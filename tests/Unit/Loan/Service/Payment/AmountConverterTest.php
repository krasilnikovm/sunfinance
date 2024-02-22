<?php

declare(strict_types=1);

namespace App\Tests\Unit\Loan\Service\Payment;

use App\Loan\Service\Payment\AmountConverter;
use PHPUnit\Framework\TestCase;

final class AmountConverterTest extends TestCase
{
    private const string AMOUNT = '45.55';

    public function testShouldParseAmount(): void
    {
        $expected = 45_55;
        $actual = AmountConverter::toInt(self::AMOUNT);

        self::assertEquals($expected, $actual);
    }

    public function testShouldThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        AmountConverter::toInt('asdfasf');
    }

    public function testShouldConvertAmountToString(): void
    {
        $expected = '45.55';
        $actual = AmountConverter::toString(45_55);

        self::assertEquals($expected, $actual);
    }
}
