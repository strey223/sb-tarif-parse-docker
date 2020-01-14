<?php

namespace App\Acme;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class Sbis
{
    /** @var string Домаин к СБИС */
    const URL_DOMAIN = 'https://sbis.ru';
    /** @var float|int Хранения Кэша в секудах */
    const STORAGE_CACHE_SECOND = 60 * 60 * 24 * 2;

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

                $query = $parseUrl['query'] ?? ''; // После ?

                if ($path) {
                    return;
                }

                $parsePathFile = pathinfo($path);

                $extension = $parsePathFile['extension'] ?? ''; // php
                $nameFile = $parsePathFile['basename'] ?? ''; // lib.inc.php
                $pathFile = $parsePathFile['dirname'] ?? ''; // путь к файлу /www/htdocs/inc

                if (!$nameFile || !$pathFile ||
                    !$extension || !in_array($extension, ['js', 'css'])) {
                    return;
                }
                $assetsPath = Singleton::app()->basePath() . 'assets/';

                $queryDirectory = $assetsPath;
                if ($query) {
                    $queryDirectory = $assetsPath . $query . '/';
                    $isQueryDirectory = file_exists($queryDirectory);
                    if (!$isQueryDirectory) {
                        mkdir($queryDirectory);
                    }
                }

                $pathToFile = $queryDirectory . $nameFile;

                if (!file_exists($pathToFile) || (time() - fileatime($pathToFile)) > self::STORAGE_CACHE_SECOND) {

                    $responseHref = (new Client())->request('GET', $href);
                    $contentFile = $responseHref->getBody();
                    file_get_contents($contentFile);
                }
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
        $response = $request->request($requestMethod, $url);
        $contentType = $response->getHeader('content-type')[0] ?? 'text/html';
        $html = $response->getBody()->getContents();
        $time = time() - $time;
        header("Content-Type: $contentType");
        header("X-Funny-Timer: " . $time);
        echo $html;
    }
}