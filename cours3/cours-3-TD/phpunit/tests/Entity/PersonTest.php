<?php

namespace Tests\Entity;

use App\Entity\Person;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    public function testConstructInitializesNameAndWallet(): void
    {
        $person = new Person('Alice', 'EUR');

        $this->assertSame('Alice', $person->getName());
        $this->assertSame('EUR', $person->getWallet()->getCurrency());
        $this->assertSame(0.0, $person->getWallet()->getBalance());
    }

    public function testHasFundReturnsFalseThenTrue(): void
    {
        $person = new Person('Alice', 'EUR');

        $this->assertFalse($person->hasFund());

        $person->getWallet()->addFund(1.0);
        $this->assertTrue($person->hasFund());
    }

    public function testTransfertFundMovesBalance(): void
    {
        $sender = new Person('Alice', 'EUR');
        $receiver = new Person('Bob', 'EUR');

        $sender->getWallet()->addFund(10.0);
        $sender->transfertFund(4.0, $receiver);

        $this->assertSame(6.0, $sender->getWallet()->getBalance());
        $this->assertSame(4.0, $receiver->getWallet()->getBalance());
    }

    public function testTransfertFundRejectsDifferentCurrency(): void
    {
        $sender = new Person('Alice', 'EUR');
        $receiver = new Person('Bob', 'USD');

        $sender->getWallet()->addFund(10.0);

        $this->expectException(\Exception::class);
        $sender->transfertFund(4.0, $receiver);
    }

    public function testDivideWalletSplitsBalanceWithRemainder(): void
    {
        $owner = new Person('Owner', 'EUR');
        $owner->getWallet()->addFund(10.0);

        $first = new Person('First', 'EUR');
        $second = new Person('Second', 'EUR');
        $third = new Person('Third', 'EUR');
        $ignored = new Person('Ignored', 'USD');

        $owner->divideWallet([$first, $second, $third, $ignored]);

        $this->assertEquals(3.34, $first->getWallet()->getBalance(), 0.0001);
        $this->assertEquals(3.33, $second->getWallet()->getBalance(), 0.0001);
        $this->assertEquals(3.33, $third->getWallet()->getBalance(), 0.0001);
        $this->assertSame(0.0, $ignored->getWallet()->getBalance());
        $this->assertEquals(0.0, $owner->getWallet()->getBalance(), 0.0001);
    }

    public function testBuyProductRemovesFunds(): void
    {
        $person = new Person('Alice', 'USD');
        $person->getWallet()->addFund(10.0);
        $product = new Product('Coffee', ['USD' => 4.0], 'food');

        $person->buyProduct($product);

        $this->assertSame(6.0, $person->getWallet()->getBalance());
    }

    public function testBuyProductRejectsUnsupportedCurrency(): void
    {
        $person = new Person('Alice', 'EUR');
        $product = new Product('Coffee', ['USD' => 4.0], 'food');

        $this->expectException(\Exception::class);
        $person->buyProduct($product);
    }
}
