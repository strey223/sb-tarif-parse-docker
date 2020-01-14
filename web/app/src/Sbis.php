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
        $status = $response->getStatusCode();
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


        /*$crawler->filter('[href]')
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

                if ($href && is_string($href)) {
                    if ($href[0] == '/') {
                        $href = self::URL_DOMAIN . $href;
                    }
                    $domElement->setAttribute('href', $href);
                    return;
                }
            });*/

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
        echo $content;

    }

    function redirectSbis()
    {
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
        $response = $request->request($requestMethod, $url, [
     //       'headers'  => ['content-type' => $contentType],
        ]);
        $contentType = $response->getHeader('content-type')[0] ?? 'text/html';

        $html = $response->getBody()->getContents();
        header("Content-Type: $contentType");
        echo $html;
    }
}