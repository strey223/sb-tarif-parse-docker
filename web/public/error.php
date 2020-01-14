<?php
include '../app/vendor/autoload.php';
use App\Acme\Sbis;
$curl = new Sbis();
$curl->redirectSbis();

/* $parseUrl = parse_url($url);
 $path = $parseUrl['path'] ?? '';

 $parsePathFile = pathinfo($path);
 $extension = $parsePathFile['extension'] ?? '';*/

/*$contentType = ;
switch ($extension) {
    case 'css':
        $contentType = 'text/css';
        break;
    case 'js':
        $contentType = 'text/js';
        break;
    case 'json':
        $contentType = 'application/json';
}*/
