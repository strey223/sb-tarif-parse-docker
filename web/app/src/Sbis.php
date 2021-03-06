<?php

namespace App\Acme;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class Sbis
{
    /** @var string Домаин к СБИС */
    const URL_DOMAIN = 'https://sbis.ru';
    /** @var float|int Хранения Кэша в секудах 2 суток */
    const STORAGE_CACHE_SECOND = 172800;

    public function hello()
    {
        $curl = new Client();

        $response = $curl->request('GET', self::URL_DOMAIN . '/tariffs?tab=tenders');
        $contentType = $response->getHeader('content-type')[0] ?? 'text/html';
        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);
        /** @var \DOMDocument $domDocument */
        $domDocument = $crawler->getNode(0)->parentNode;

        $div = $domDocument->createElement('link');
        $div->setAttribute('rel', 'stylesheet');
        $div->setAttribute('type', 'text/css');
        $div->setAttribute('href', '/removeElement.css?css');

        /** @var \DOMDocument $head */
        $head = $crawler->filter('head')->getNode(0);
        $head->appendChild($div);

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

        $crawler->filter('.billing-PriceList__tabList-info-wrapper.controls-Scroll__userContent')
            ->each(function (Crawler $crawler) {
                foreach ($crawler as $node) {
                    $node->parentNode->removeChild($node);
                }
            });
        $crawler->filter('.sbis_ru-Header__fixed')
            ->each(function (Crawler $crawler) {
                foreach ($crawler as $node) {
                    $node->parentNode->removeChild($node);
                }
            });
        $crawler->filter('.sbis_ru-Footer sbis_ru-background.sbis_ru-section')
            ->each(function (Crawler $crawler) {
                foreach ($crawler as $node) {
                    $node->parentNode->removeChild($node);
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

                if (!$nameFile == 'removeElement.css') {
                    $this->redirectSbis();
                }

                if (!file_exists($pathToFile) || (time() - fileatime($pathToFile)) > self::STORAGE_CACHE_SECOND) {

                    $responseHref = (new Client())->request('GET', self::URL_DOMAIN . $href);
                    $contentFile = $responseHref->getBody();
                    file_put_contents($pathToFile, $contentFile);
                }
            });

        $content = $crawler->outerHtml();

        header("Content-Type: $contentType");
        echo $content;

    }


    /**
     * return @void
     */
    public function getFileOrRebirectSbis() {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (strpos($uri, '/tariffs?') !== false) {
            return $this->hello();
        }


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