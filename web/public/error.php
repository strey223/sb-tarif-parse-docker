<?php
include '../app/vendor/autoload.php';
use App\Acme\Sbis;
$curl = new Sbis();
$curl->getFileOrRebirectSbis();