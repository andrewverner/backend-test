<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\CalculatePriceRequest;
use App\Dto\PurchaseRequest;
use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\CountriesCodeEnum;
use App\Exception\ResourceNotFoundException;
use App\Exception\ValidationException;
use App\Payment\PaymentProcessorRegistry;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Validator\ValidatorService;

final readonly class OrderApplicationService
{
    public function __construct(
        private ValidatorService $validator,
        private ProductRepository $productRepository,
        private CouponRepository $couponRepository,
        private PriceCalculator $priceCalculator,
        private PaymentProcessorRegistry $paymentProcessorRegistry,
    ) {
    }

    public function calculatePrice(CalculatePriceRequest $request): float
    {
        $this->assertValid($request);

        return $this->calculatePriceFor(
            productId: $request->product,
            couponCode: $request->couponCode,
            taxNumber: $request->taxNumber,
        );
    }

    public function purchase(PurchaseRequest $request): void
    {
        $this->assertValid($request);

        $price = $this->calculatePriceFor(
            productId: $request->product,
            couponCode: $request->couponCode,
            taxNumber: $request->taxNumber,
        );

        $paymentProcessor = $this->paymentProcessorRegistry->get($request->paymentProcessor);
        $paymentProcessor->pay($price);
    }

    private function assertValid(object $dto): void
    {
        if (!$this->validator->validate($dto)) {
            throw new ValidationException($this->validator->getErrors());
        }
    }

    private function calculatePriceFor(int $productId, ?string $couponCode, string $taxNumber): float
    {
        $product = $this->getProduct($productId);
        $coupon = $this->getCoupon($couponCode);
        $countryCode = $this->extractCountryCode($taxNumber);

        return $this->priceCalculator->calculate(
            price: $product->getPrice(),
            countryCode: $countryCode,
            coupon: $coupon,
        );
    }

    private function getProduct(int $productId): Product
    {
        $product = $this->productRepository->find($productId);

        if (!$product) {
            throw ResourceNotFoundException::forField('product');
        }

        return $product;
    }

    private function getCoupon(?string $couponCode): ?Coupon
    {
        if (!$couponCode) {
            return null;
        }

        $coupon = $this->couponRepository->findOneBy(['code' => $couponCode]);

        if (!$coupon) {
            throw ResourceNotFoundException::forField('coupon');
        }

        return $coupon;
    }

    private function extractCountryCode(string $taxNumber): CountriesCodeEnum
    {
        return CountriesCodeEnum::from(substr($taxNumber, 0, 2));
    }
}
