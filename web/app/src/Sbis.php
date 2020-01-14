<?php

namespace App\Acme;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class Sbis
{
    /** @var string Домаин к СБИС */
    const URL_DOMAIN = 'https://sbis.ru';

    public function hello()
    {
        $curl = new Client();
        $response = $curl->request('GET', self::URL_DOMAIN . '/tariffs?tab=tenders');
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        $crawler->filter('script')
            ->each(function ($node) {
                $domElement = $node->getNode(0);
                $src = $domElement->getAttribute('src');

                if ($src && is_string($src)) {
                    if ($src[0] == '/') {
                        $src = self::URL_DOMAIN . $src;
                    }
                    $domElement->setAttribute('src', $src);
                    return;
                }
            });


        $crawler->filter('[href]')
            ->each(function ($node) {
                $domElement = $node->getNode(0);
                $href = $domElement->getAttribute('href');

                if (!$href || !is_string($href)) {
                    return;
                }

                if ($href[0] !== '/') {
                    return;
                }

                $url = self::URL_DOMAIN . $href;

                $parseUrl = parse_url($url);
                $path = $parseUrl['path'] ?? '';
                if ($path) {
                    return;
                }

                $parsePathFile = pathinfo($path);

                $extension = $parsePathFile['extension'] ?? ''; // php
                $nameFile = $parsePathFile['basename'] ?? ''; // lib.inc.php
                $pathFile = $parsePathFile['dirname'] ?? ''; // путь к файлу /www/htdocs/inc

                //filemtime - время изменения файла

                if (!$nameFile || !$pathFile ||
                    !$extension || $extension !== 'js' || $extension !== 'css') {
                    return;
                }

                //$folderHash = md5($pathFile);

                $assetsPath = Singleton::app()->basePath() . 'assets/';

                $checkFile = $assetsPath . $nameFile;

                /*if ($href && is_string($href)) {
                    if ($href[0] == '/') {
                        $href = self::URL_DOMAIN . $href;
                    }
                    $domElement->setAttribute('href', $href);
                    return;
                }*/
            });

      /*$crawler->filter('head link')
        ->each(function ($node) {
            $domElement = $node->getNode(0);
            $href = $domElement->getAttribute('href');

            if ($href && is_string($href)) {
                if ($href[0] == '/') {
                    $href = self::URL_DOMAIN . $href;
                }
                $domElement->setAttribute('href', $href);
                return;
            }
        });*/


        $content = $crawler->html();
       // echo $html;
        //echo $content1;

        //$content = $crawler->filter('.billing-PriceList__service-detail.billing-PriceList__tip')->last()->html();
        header('Content-Type: text/html');
        echo $content;

    }

    function redirectSbis()
    {
        $time = time();
        $request = new Client();
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $url = self::URL_DOMAIN . $uri;
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
        $response = $request->request($requestMethod, $url);
        $contentType = $response->getHeader('content-type')[0] ?? 'text/html';
        $html = $response->getBody()->getContents();
        $time = time() - $time;
        header("Content-Type: $contentType");
        header("X-Funny-Timer: " . $time);
        echo $html;
    }
}