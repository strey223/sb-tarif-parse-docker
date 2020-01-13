<?php

include '../app/vendor/autoload.php';
use App\Acme\Foo;
use App\Acme\Sbis;

$foo = new Foo();
$curl = new Sbis();

$curl->hello();

?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Docker <?php echo $foo->getName(); ?></title>
    </head>
    <body>
        <h1>Docker <?php echo $foo->getName(); ?></h1>
    </body>
</html>
