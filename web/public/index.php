<?php

include '../app/vendor/autoload.php';
include '../config/config.php';
use App\Acme\Sbis;
use App\Acme\Singleton;

throw new Exception(403);
Singleton::app($config);

$curl = new Sbis();

$curl->hello();