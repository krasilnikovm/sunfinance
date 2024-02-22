<?php

declare(strict_types=1);

namespace App\Loan\Validator;

use App\Loan\Service\Payment\AmountConverter;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class AmountValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Amount) {
            return;
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$this->canConvert($value)) {
            $this->context->buildViolation(Amount::INVALID_AMOUNT_FORMAT_MESSAGE)
                ->setParameter('{{ value }}', $value)
                ->addViolation();

            return;
        }

        if (!$this->isPositive($value)) {
            $this->context->buildViolation(Amount::NEGATIVE_AMOUNT_MESSAGE)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }

    private function canConvert(string $value): bool
    {
        try {
            AmountConverter::toInt($value);
        } catch (\InvalidArgumentException) {
            return false;
        }

        return true;
    }

    private function isPositive(string $value): bool
    {
        $amount = AmountConverter::toInt($value);

        return $amount > 0;
    }
}
