<?php
include '../app/vendor/autoload.php';
include '../config/config.php';

throw new Exception('Не найдена страница', 403);

use App\Acme\Singleton;
use App\Acme\Sbis;
Singleton::app($config);
$curl = new Sbis();
$curl->getFileOrRebirectSbis();