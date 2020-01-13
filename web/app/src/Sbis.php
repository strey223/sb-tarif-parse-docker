<?php

namespace App\Acme;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class Sbis
{
    public function hello()
    {
        $curl = new Client();
        $response = $curl->request('GET', 'https://sbis.ru/tariffs?tab=tenders');
        $status = $response->getStatusCode();
        var_dump($status);
        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);
        $content2 = $crawler->filter('.billing-PriceList__service-detail.billing-PriceList__tip')->last()->html();
        echo $content2;
    }
}