<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Config;

$config = Config::getInstance();
$secondConfig = Config::getInstance();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Singleton</title>
</head>
<body>
    <p>Debug: <?= $config->get('debug') ? 'true' : 'false'; ?></p>
    <p>Meme instance: <?= $config === $secondConfig ? 'oui' : 'non'; ?></p>
</body>
</html>
