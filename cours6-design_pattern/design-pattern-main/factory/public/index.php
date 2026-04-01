<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Factory\VehicleFactory;

$vehicleByType = VehicleFactory::create('car');
$shortTripVehicle = VehicleFactory::createForTrip(10, 5);
$longTripVehicle = VehicleFactory::createForTrip(80, 25);
$cargoVehicle = VehicleFactory::createForTrip(120, 250);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Factory</title>
</head>
<body>
    <ul>
        <li>Creation par type: <?= $vehicleByType->getName(); ?>, cost/km <?= $vehicleByType->getCostPerKm(); ?>, fuel <?= $vehicleByType->getFuelType(); ?></li>
        <li>Trip 10 km / 5 kg: <?= $shortTripVehicle->getName(); ?></li>
        <li>Trip 80 km / 25 kg: <?= $longTripVehicle->getName(); ?></li>
        <li>Trip 120 km / 250 kg: <?= $cargoVehicle->getName(); ?></li>
    </ul>
</body>
</html>
