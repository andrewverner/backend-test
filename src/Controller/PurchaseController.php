<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\PurchaseRequest;
use App\Enum\CountriesCodeEnum;
use App\Payment\PaymentProcessorRegistry;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Service\PriceCalculator;
use App\Validator\ValidatorService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class PurchaseController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorService $validator,
        private ProductRepository $productRepository,
        private CouponRepository $couponRepository,
        private PriceCalculator $priceCalculator,
        private PaymentProcessorRegistry $paymentProcessorRegistry,
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

        if (!$this->validator->validate($dto)) {
            return new JsonResponse(data: ['errors' => $this->validator->getErrors()], status: 422);
        }

        $product = $this->productRepository->find(id: $dto->product);

        if (!$product) {
            return new JsonResponse(data: ['errors' => ['product' => ['Not found']]], status: 422);
        }

        $coupon = $this->couponRepository->findOneBy(['code' => $dto->couponCode]);

        if ($dto->couponCode && !$coupon) {
            return new JsonResponse(data: ['errors' => ['coupon' => ['Not found']]], status: 422);
        }

        $price = $this->priceCalculator->calculate(
            price: $product->getPrice(),
            countryCode: CountriesCodeEnum::from(substr($dto->taxNumber, 0, 2)),
            coupon: $coupon,
        );

        $paymentProcessor = $this->paymentProcessorRegistry->get($dto->paymentProcessor);
        $paymentProcessor->pay(price: $price);

        return new JsonResponse(data: ['message' => 'Payed via ' . $paymentProcessor::class], status: 200);
    }
}
