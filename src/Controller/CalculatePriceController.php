<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\CalculatePriceRequest;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class CalculatePriceController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private ProductRepository $productRepository,
    ) {
    }

    #[Route(
        path: '/calculate-price',
        name: 'calculate-price',
        methods: ['POST'],
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $dto = $this->serializer->deserialize(
            data: $request->getContent(),
            type: CalculatePriceRequest::class,
            format: 'json',
        );

        $violations = $this->validator->validate(value: $dto);

        if (count($violations) > 0) {
            $errors = [];

            foreach ($violations as $violation) {
                $field = $violation->getPropertyPath() ?: 'body';
                $errors[$field][] = $violation->getMessage();
            }

            return new JsonResponse(data: ['errors' => $errors], status: 422);
        }

        $product = $this->productRepository->find(id: $dto->product);

        if (!$product) {
            return new JsonResponse(data: ['errors' => ['product' => ['Not found']]], status: 422);
        }

        return new JsonResponse(data: []);
    }
}