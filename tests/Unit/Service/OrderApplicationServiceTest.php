<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Dto\CalculatePriceRequest;
use App\Dto\PurchaseRequest;
use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\CountriesCodeEnum;
use App\Enum\CouponTypeEnum;
use App\Enum\PaymentProcessorsEnum;
use App\Exception\ResourceNotFoundException;
use App\Exception\ValidationException;
use App\Payment\PaymentProcessorAdapterInterface;
use App\Payment\PaymentProcessorRegistry;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Service\OrderApplicationService;
use App\Service\PriceCalculator;
use App\Validator\ValidatorService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class OrderApplicationServiceTest extends TestCase
{
    private ValidatorService&MockObject $validator;

    private ProductRepository&MockObject $productRepository;

    private CouponRepository&MockObject $couponRepository;

    private PriceCalculator&MockObject $priceCalculator;

    private PaymentProcessorRegistry&MockObject $paymentProcessorRegistry;

    private OrderApplicationService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorService::class);
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->couponRepository = $this->createMock(CouponRepository::class);
        $this->priceCalculator = $this->createMock(PriceCalculator::class);
        $this->paymentProcessorRegistry = $this->createMock(PaymentProcessorRegistry::class);

        $this->service = new OrderApplicationService(
            $this->validator,
            $this->productRepository,
            $this->couponRepository,
            $this->priceCalculator,
            $this->paymentProcessorRegistry,
        );
    }

    public function testCalculatePriceReturnsCalculatedAmount(): void
    {
        $request = new CalculatePriceRequest(1, 'DE123456789', 'D15');
        $product = (new Product())->setPrice(100);

        $coupon = (new Coupon())
            ->setType(CouponTypeEnum::FIXED_DISCOUNT)
            ->setValue(5);

        $this->validator
            ->expects(self::once())
            ->method('validate')
            ->with($request)
            ->willReturn(true);

        $this->productRepository
            ->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($product);

        $this->couponRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'D15'])
            ->willReturn($coupon);

        $this->priceCalculator
            ->expects(self::once())
            ->method('calculate')
            ->with(100, CountriesCodeEnum::DE, $coupon)
            ->willReturn(113.05);

        $price = $this->service->calculatePrice($request);

        self::assertSame(113.05, $price);
    }

    public function testCalculatePriceWithoutCouponSkipsLookup(): void
    {
        $request = new CalculatePriceRequest(2, 'DE123456789', null);
        $product = (new Product())->setPrice(100);

        $this->validator
            ->expects(self::once())
            ->method('validate')
            ->with($request)
            ->willReturn(true);

        $this->productRepository
            ->expects(self::once())
            ->method('find')
            ->with(2)
            ->willReturn($product);

        $this->couponRepository
            ->expects(self::never())
            ->method('findOneBy');

        $this->priceCalculator
            ->expects(self::once())
            ->method('calculate')
            ->with(100, CountriesCodeEnum::DE, null)
            ->willReturn(119.0);

        $price = $this->service->calculatePrice($request);

        self::assertSame(119.0, $price);
    }

    public function testCalculatePriceThrowsWhenProductMissing(): void
    {
        $request = new CalculatePriceRequest(999, 'DE123456789');

        $this->validator
            ->method('validate')
            ->willReturn(true);

        $this->productRepository
            ->method('find')
            ->willReturn(null);

        $this->expectException(ResourceNotFoundException::class);

        $this->service->calculatePrice($request);
    }

    public function testCalculatePriceThrowsWhenCouponMissing(): void
    {
        $request = new CalculatePriceRequest(1, 'DE123456789', 'D15');
        $product = (new Product())->setPrice(100);

        $this->validator
            ->method('validate')
            ->willReturn(true);

        $this->productRepository
            ->method('find')
            ->willReturn($product);

        $this->couponRepository
            ->method('findOneBy')
            ->willReturn(null);

        $this->expectException(ResourceNotFoundException::class);

        $this->service->calculatePrice($request);
    }

    public function testCalculatePriceThrowsWhenValidationFails(): void
    {
        $request = new CalculatePriceRequest(1, 'DE123456789');

        $this->validator
            ->expects(self::once())
            ->method('validate')
            ->with($request)
            ->willReturn(false);

        $this->validator
            ->method('getErrors')
            ->willReturn(['field' => ['Invalid']]);

        $this->expectException(ValidationException::class);

        $this->service->calculatePrice($request);
    }

    public function testPurchaseProcessesPaymentWithCalculatedPrice(): void
    {
        $request = new PurchaseRequest(1, 'DE123456789', PaymentProcessorsEnum::PAYPAL, 'D15');
        $product = (new Product())->setPrice(100);
        $coupon = (new Coupon())
            ->setType(CouponTypeEnum::PERCENT_DISCOUNT)
            ->setValue(10);

        $this->validator
            ->method('validate')
            ->with($request)
            ->willReturn(true);

        $this->productRepository
            ->method('find')
            ->willReturn($product);

        $this->couponRepository
            ->method('findOneBy')
            ->willReturn($coupon);

        $this->priceCalculator
            ->expects(self::once())
            ->method('calculate')
            ->with(100, CountriesCodeEnum::DE, $coupon)
            ->willReturn(107.1);

        $paymentProcessor = $this->createMock(PaymentProcessorAdapterInterface::class);
        $paymentProcessor
            ->expects(self::once())
            ->method('pay')
            ->with(107.1);

        $this->paymentProcessorRegistry
            ->method('get')
            ->with(PaymentProcessorsEnum::PAYPAL)
            ->willReturn($paymentProcessor);

        $this->service->purchase($request);
    }
}
