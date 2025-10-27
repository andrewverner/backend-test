<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\CalculatePriceRequest;
use App\Service\OrderApplicationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class CalculatePriceController
{
    public function __construct(
        private SerializerInterface $serializer,
        private OrderApplicationService $orderApplicationService,
    ) {
    }

    #[Route(
        path: '/calculate-price',
        name: 'calculate-price',
        methods: ['POST'],
    )]
    public function __invoke(Request $request): JsonResponse
    {
        /** @var CalculatePriceRequest $dto */
        $dto = $this->serializer->deserialize(
            data: $request->getContent(),
            type: CalculatePriceRequest::class,
            format: 'json',
        );

        $price = $this->orderApplicationService->calculatePrice($dto);

        return new JsonResponse(data: ['price' => $price], status: 200);
    }
}