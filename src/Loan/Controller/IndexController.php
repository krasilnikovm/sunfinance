<?php

declare(strict_types=1);

namespace App\Loan\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class IndexController
{
    #[Route('/', name: 'sunfinance_index')]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['SunFinance']);
    }
}
