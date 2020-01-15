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
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $response = $curl->request('GET', self::URL_DOMAIN . $uri);
        $contentType = $response->getHeader('content-type')[0] ?? 'text/html';
        $html = $response->getBody()->getContents();
        /*header("Content-Type: $contentType");
        echo $html;
        exit;*/
        $crawler = new Crawler($html);

     /*   $crawler->filter('script')
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
            });*/

       /* $crawler->filter('.billing-PriceList__tabList-info-wrapper.controls-Scroll__userContent')
            ->each(function ($node) {
                $domElement = $node->getNode(0);
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

                if (!$path) {
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

                $queryDirectory = Singleton::app()->basePath() . 'assets/';
                if ($query) {
                    $queryDirectory .= $query . '/';
                    $isQueryDirectory = file_exists($queryDirectory);
                    if (!$isQueryDirectory) {
                        mkdir($queryDirectory);
                    }
                }

                $pathToFile = $queryDirectory . $nameFile;

                if (!file_exists($pathToFile) || (time() - fileatime($pathToFile)) > self::STORAGE_CACHE_SECOND) {

                    $responseHref = (new Client())->request('GET', self::URL_DOMAIN . $href);
                    $contentFile = $responseHref->getBody();
                    file_put_contents($pathToFile, $contentFile);
                }
            });*/

        $content = $crawler->html();

        //$content = $crawler->filter('.billing-PriceList__service-detail.billing-PriceList__tip')->last()->html();
        header("Content-Type: $contentType");
        echo $content;

    }


    /**
     * return @void
     */
    public function getFileOrRebirectSbis() {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($requestMethod != 'GET') {
            return $this->redirectSbis();
        }
        $parseUrl = parse_url($uri);

        $path = $parseUrl['path'] ?? '';
        $query = $parseUrl['query'] ?? ''; // После ?

        if (!$path) {
            return $this->redirectSbis();
        }


        $parsePathFile = pathinfo($path);

        $extension = $parsePathFile['extension'] ?? ''; // php
        $nameFile = $parsePathFile['basename'] ?? ''; // lib.inc.php
        $pathFile = $parsePathFile['dirname'] ?? ''; // путь к файлу /www/htdocs/inc

        if (!$nameFile || !$pathFile ||
            !$extension || !in_array($extension, ['js', 'css'])) {
            return $this->redirectSbis();
        }

        $queryDirectory = Singleton::app()->basePath() . 'assets/';
        if ($query) {
            $queryDirectory .= $query . '/';
            $isQueryDirectory = file_exists($queryDirectory);
            if (!$isQueryDirectory) {
                return $this->redirectSbis();
            }
        }
        $pathToFile = $queryDirectory . $nameFile;

        if (!file_exists($pathToFile)) {
            return $this->redirectSbis();
        }
        $contentType = 'text/html';
        switch ($extension) {
            case 'css':
                $contentType = 'text/css';
                break;
            case 'js':
                $contentType = 'text/js';
                break;
            case 'json':
                $contentType = 'application/json';
        }
        header("Content-Type: $contentType");
        echo  file_get_contents($pathToFile);
    }

    /**
     * @return void
     */
    function redirectSbis()
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $request = new Client();

        $url = self::URL_DOMAIN . $uri;
        $response = $request->request($requestMethod, $url);

        $contentType = $response->getHeader('content-type')[0] ?? 'text/html';
        $html = $response->getBody()->getContents();

        header("Content-Type: $contentType");

        echo $html;
    }
}