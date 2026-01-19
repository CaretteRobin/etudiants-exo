<?php

namespace Tests\Entity;

use App\Entity\Person;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    public function testConstructInitializesNameAndWallet(): void
    {
        $person = $this->createPerson();

        $this->assertSame('Alice', $person->getName());
        $this->assertSame('EUR', $person->getWallet()->getCurrency());
        $this->assertSame(0.0, $person->getWallet()->getBalance());
    }

    public function testHasFundReturnsFalseThenTrue(): void
    {
        $person = $this->createPerson();

        $this->assertFalse($person->hasFund());

        $person->getWallet()->addFund(1.0);
        $this->assertTrue($person->hasFund());
    }

    public function testTransfertFundMovesBalance(): void
    {
        $sender = $this->createPerson();
        $receiver = $this->createPerson('Bob');

        $sender->getWallet()->addFund(10.0);
        $sender->transfertFund(4.0, $receiver);

        $this->assertSame(6.0, $sender->getWallet()->getBalance());
        $this->assertSame(4.0, $receiver->getWallet()->getBalance());
    }

    public function testTransfertFundRejectsDifferentCurrency(): void
    {
        $sender = $this->createPerson();
        $receiver = $this->createPerson('Bob', 'USD');

        $sender->getWallet()->addFund(10.0);

        $this->expectException(\Exception::class);
        $sender->transfertFund(4.0, $receiver);
    }

    public function testDivideWalletSplitsBalanceWithRemainder(): void
    {
        $owner = $this->createPerson('Owner');
        $owner->getWallet()->addFund(10.0);

        $first = $this->createPerson('First');
        $second = $this->createPerson('Second');
        $third = $this->createPerson('Third');
        $ignored = $this->createPerson('Ignored', 'USD');

        $owner->divideWallet([$first, $second, $third, $ignored]);

        $this->assertEquals(3.34, $first->getWallet()->getBalance(), 0.0001);
        $this->assertEquals(3.33, $second->getWallet()->getBalance(), 0.0001);
        $this->assertEquals(3.33, $third->getWallet()->getBalance(), 0.0001);
        $this->assertSame(0.0, $ignored->getWallet()->getBalance());
        $this->assertEquals(0.0, $owner->getWallet()->getBalance(), 0.0001);
    }

    public function testBuyProductRemovesFunds(): void
    {
        $person = $this->createPerson('Alice', 'USD');
        $person->getWallet()->addFund(10.0);
        $product = $this->createProduct(['USD' => 4.0]);

        $person->buyProduct($product);

        $this->assertSame(6.0, $person->getWallet()->getBalance());
    }

    public function testBuyProductRejectsUnsupportedCurrency(): void
    {
        $person = $this->createPerson();
        $product = $this->createProduct(['USD' => 4.0]);

        $this->expectException(\Exception::class);
        $person->buyProduct($product);
    }

    private function createPerson(string $name = 'Alice', string $currency = 'EUR'): Person
    {
        return new Person($name, $currency);
    }

    private function createProduct(array $prices, string $type = 'food', string $name = 'Coffee'): Product
    {
        return new Product($name, $prices, $type);
    }
}
