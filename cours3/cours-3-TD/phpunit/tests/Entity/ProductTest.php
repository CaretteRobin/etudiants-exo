<?php

namespace Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testConstructInitializesProduct(): void
    {
        $product = $this->createProduct();

        $this->assertSame('Coffee', $product->getName());
        $this->assertSame('food', $product->getType());
        $this->assertSame(['USD' => 4.2, 'EUR' => 3.9], $product->getPrices());
    }

    #[DataProvider('invalidTypeProvider')]
    public function testSetTypeRejectsInvalid(string $type): void
    {
        $product = $this->createProduct(['USD' => 4.2]);

        $this->expectException(\Exception::class);
        $product->setType($type);
    }

    #[DataProvider('tvaProvider')]
    public function testGetTVAReturnsExpectedRate(string $type, float $expected): void
    {
        $product = $this->createProduct(['USD' => 4.2], $type);

        $this->assertSame($expected, $product->getTVA());
    }

    public function testListCurrenciesReturnsPriceKeys(): void
    {
        $product = $this->createProduct();

        $this->assertSame(['USD', 'EUR'], $product->listCurrencies());
    }

    public function testSetPricesFiltersInvalidEntries(): void
    {
        $product = $this->createProduct(['USD' => 4.2]);
        $product->setPrices([
            'EUR' => 3.9,
            'USD' => -1,
            'JPY' => 10,
        ]);

        $this->assertSame(['USD' => 4.2, 'EUR' => 3.9], $product->getPrices());
    }

    public function testGetPriceReturnsValue(): void
    {
        $product = $this->createProduct(['USD' => 4.2]);

        $this->assertSame(4.2, $product->getPrice('USD'));
    }

    #[DataProvider('invalidCurrencyProvider')]
    public function testGetPriceRejectsInvalidCurrency(string $currency): void
    {
        $product = $this->createProduct(['USD' => 4.2]);

        $this->expectException(\Exception::class);
        $product->getPrice($currency);
    }

    public function testGetPriceRejectsUnavailableCurrency(): void
    {
        $product = $this->createProduct(['USD' => 4.2]);

        $this->expectException(\Exception::class);
        $product->getPrice('EUR');
    }

    public static function invalidTypeProvider(): array
    {
        return [
            [''],
            ['vehicle'],
            ['foodie'],
        ];
    }

    public static function tvaProvider(): array
    {
        return [
            ['food', 0.1],
            ['tech', 0.2],
            ['other', 0.2],
        ];
    }

    public static function invalidCurrencyProvider(): array
    {
        return [
            ['JPY'],
            ['BTC'],
        ];
    }

    private function createProduct(
        array $prices = ['USD' => 4.2, 'EUR' => 3.9],
        string $type = 'food',
        string $name = 'Coffee'
    ): Product {
        return new Product($name, $prices, $type);
    }
}
