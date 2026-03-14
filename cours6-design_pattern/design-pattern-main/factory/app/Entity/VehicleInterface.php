<?php

namespace App\Entity;

interface VehicleInterface
{
    public function getName();

    public function getCostPerKm();

    public function getFuelType();
}
