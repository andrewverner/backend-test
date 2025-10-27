<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\PurchaseRequest;
use App\Service\OrderApplicationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class PurchaseController
{
    public function __construct(
        private SerializerInterface $serializer,
        private OrderApplicationService $orderApplicationService,
    ) {
    }

    #[Route(
        path: '/purchase',
        name: 'purchase',
        methods: ['POST'],
    )]
    public function __invoke(Request $request): JsonResponse
    {
        /** @var PurchaseRequest::class $dto */
        $dto = $this->serializer->deserialize(
            data: $request->getContent(),
            type: PurchaseRequest::class,
            format: 'json',
        );

        $this->orderApplicationService->purchase($dto);

        return new JsonResponse(data: ['message' => 'OK'], status: 200);
    }
}
