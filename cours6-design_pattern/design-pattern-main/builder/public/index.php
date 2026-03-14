<?php
require __DIR__ . '/../vendor/autoload.php';

use App\MySQLQueryBuilder;

$firstQuery = (new MySQLQueryBuilder())
    ->select(['id', 'name', 'email'])
    ->from('users')
    ->where('active = 1')
    ->build();

$secondQuery = (new MySQLQueryBuilder())
    ->select(['title', 'author'])
    ->from('books')
    ->where("category = 'php'")
    ->where('published = 1')
    ->build();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Builder</title>
</head>
<body>
    <ul>
        <li><?= $firstQuery; ?></li>
        <li><?= $secondQuery; ?></li>
    </ul>
</body>
</html>
