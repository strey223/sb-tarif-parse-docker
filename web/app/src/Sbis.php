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


       /* $crawler->filter('head link')
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
            });
*/
        $content = $crawler->html();
        echo $content;
        //echo $content1;

        //$content = $crawler->filter('.billing-PriceList__service-detail.billing-PriceList__tip')->last()->html();
        //echo $content;

    }

    function redirectSbis()
    {
        $curl = new Client();
        $request = $_SERVER['REQUEST_URI'] ?? '';
        $response = $curl->request('GET', self::URL_DOMAIN . $request);
        $html = $response->getBody()->getContents();

        echo $html;
    }
}