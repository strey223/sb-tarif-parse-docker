<?php

include '../app/vendor/autoload.php';
include '../config/config.php';
use App\Acme\Sbis;
use App\Acme\Singleton;

Singleton::app($config);

$curl = new Sbis();

$curl->hello();