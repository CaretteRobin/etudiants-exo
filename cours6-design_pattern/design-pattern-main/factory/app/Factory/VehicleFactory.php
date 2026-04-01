<?php

namespace App\Factory;

use App\Entity\Bicycle;
use App\Entity\Car;
use App\Entity\Truck;
use App\Entity\VehicleInterface;
use InvalidArgumentException;

class VehicleFactory
{
    public static function create(string $type): VehicleInterface
    {
        switch (strtolower($type)) {
            case 'bicycle':
                return new Bicycle(0, 'human');

            case 'car':
                return new Car(0.5, 'essence');

            case 'truck':
                return new Truck(1.2, 'diesel');

            default:
                throw new InvalidArgumentException('Unknown vehicle type.');
        }
    }

    public static function createForTrip(float $distance, float $weight): VehicleInterface
    {
        if ($weight > 200) {
            return self::create('truck');
        }

        if ($weight > 20) {
            return self::create('car');
        }

        if ($distance < 20) {
            return self::create('bicycle');
        }

        return self::create('car');
    }
}
