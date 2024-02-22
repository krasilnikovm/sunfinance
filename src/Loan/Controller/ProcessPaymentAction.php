<?php

declare(strict_types=1);

namespace App\Loan\Controller;

use App\Loan\Service\Payment\Exception\DuplicatePaymentException;
use App\Loan\Service\Payment\Exception\LoanNotFoundException;
use App\Loan\Service\Payment\Exception\ValidationException;
use App\Loan\Service\Payment\PaymentData;
use App\Loan\Service\Payment\PaymentProcessor;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
readonly class ProcessPaymentAction
{
    public function __construct(
        private PaymentProcessor $paymentProcessor,
    ) {
    }

    #[Route('/api/payment', name: 'sunfinance_process_payment', methods: ['POST'])]
    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Returns 204 in case of success',
    )]
    #[OA\Response(
        response: Response::HTTP_CONFLICT,
        description: 'Returns 409 status code in case of duplicate entry',
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Returns 400 status code in case of validation error',
    )]
    #[OA\Tag(name: 'payment')]
    public function __invoke(
        #[MapRequestPayload(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] PaymentData $paymentData
    ): Response {
        try {
            $this->paymentProcessor->process(
                $paymentData,
            );
        } catch (DuplicatePaymentException) {
            return new JsonResponse(null, Response::HTTP_CONFLICT);
        } catch (ValidationException|LoanNotFoundException) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
