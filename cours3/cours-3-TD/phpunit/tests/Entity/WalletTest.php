<?php

namespace Tests\Entity;

use App\Entity\Wallet;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class WalletTest extends TestCase
{
    #[DataProvider('validCurrencyProvider')]
    public function testConstructInitializesBalanceAndCurrency(string $currency): void
    {
        $wallet = new Wallet($currency);

        $this->assertSame(0.0, $wallet->getBalance());
        $this->assertSame($currency, $wallet->getCurrency());
    }

    public function testSetBalanceAllowsZeroAndPositive(): void
    {
        $wallet = new Wallet('EUR');
        $wallet->setBalance(12.5);

        $this->assertSame(12.5, $wallet->getBalance());
    }

    public function testSetBalanceRejectsNegative(): void
    {
        $wallet = new Wallet('EUR');

        $this->expectException(\Exception::class);
        $wallet->setBalance(-1);
    }

    #[DataProvider('invalidCurrencyProvider')]
    public function testSetCurrencyRejectsInvalid(string $currency): void
    {
        $wallet = new Wallet('EUR');

        $this->expectException(\Exception::class);
        $wallet->setCurrency($currency);
    }

    public function testAddFundAddsAmount(): void
    {
        $wallet = new Wallet('USD');
        $wallet->addFund(10.5);

        $this->assertSame(10.5, $wallet->getBalance());
    }

    #[DataProvider('invalidAmountProvider')]
    public function testAddFundRejectsNegative(float $amount): void
    {
        $wallet = new Wallet('USD');

        $this->expectException(\Exception::class);
        $wallet->addFund($amount);
    }

    public function testRemoveFundRemovesAmount(): void
    {
        $wallet = new Wallet('USD');
        $wallet->addFund(20.0);
        $wallet->removeFund(5.5);

        $this->assertSame(14.5, $wallet->getBalance());
    }

    #[DataProvider('invalidAmountProvider')]
    public function testRemoveFundRejectsNegative(float $amount): void
    {
        $wallet = new Wallet('USD');

        $this->expectException(\Exception::class);
        $wallet->removeFund($amount);
    }

    public function testRemoveFundRejectsInsufficientFunds(): void
    {
        $wallet = new Wallet('USD');
        $wallet->addFund(4.0);

        $this->expectException(\Exception::class);
        $wallet->removeFund(10.0);
    }

    public static function validCurrencyProvider(): array
    {
        return [
            ['USD'],
            ['EUR'],
        ];
    }

    public static function invalidCurrencyProvider(): array
    {
        return [
            ['JPY'],
            [''],
            ['usd'],
        ];
    }

    public static function invalidAmountProvider(): array
    {
        return [
            [-0.01],
            [-10.0],
        ];
    }
}
