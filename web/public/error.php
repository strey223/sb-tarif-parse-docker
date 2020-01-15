<?php
include '../app/vendor/autoload.php';
include '../config/config.php';

use App\Acme\Singleton;
use App\Acme\Sbis;
Singleton::app($config);
$curl = new Sbis();
$curl->getFileOrRebirectSbis();