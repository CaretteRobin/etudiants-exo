<?php

namespace Test;

use PHPUnit\Framework\TestCase;

use App\GPUDecorator;
use App\Laptop;
use App\OLEDScreenDecorator;

class ComputerDecoratorTest extends TestCase
{
    public function testBasicLaptop()
    {
        $laptop = new Laptop();
        
        $this->assertSame(400, $laptop->getPrice());
        $this->assertSame("A laptop computer", $laptop->getDescription());
    }

    public function testLaptopWithGPU()
    {
        $laptop = new GPUDecorator(new Laptop());

        $this->assertSame(650, $laptop->getPrice());
        $this->assertSame("A laptop computer with GPU", $laptop->getDescription());
    }

    public function testLaptopWithOLEDScreen()
    {
        $laptop = new OLEDScreenDecorator(new Laptop());

        $this->assertSame(600, $laptop->getPrice());
        $this->assertSame("A laptop computer with OLED screen", $laptop->getDescription());
    }
}
