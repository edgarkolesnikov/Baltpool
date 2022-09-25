<?php

namespace App\Service;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Form\AbstractType;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UrlChecker extends AbstractType
{
    public function __construct(private HttpClientInterface $httpClient) {}

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[ArrayShape(['statusCode' => "int", 'redirectsCount' => "int", 'keywordsCount' => 'int' ])]
    public function checkUrl(string $url, string $keyword): array
    {
        try{
        //connecting to url provided in form
        $response = $this->httpClient->request(
            'GET',
            $url
        );

        //collecting needed data from url
        $statusCode = $response->getStatusCode();
        $redirectsCount = $response->getInfo()['redirect_count'];
        $content = $response->getContent();
        $keywordsCount = substr_count($content, $keyword);

        //returning array of collected data
        return [
            'statusCode' => $statusCode,
            'redirectsCount' => $redirectsCount,
            'keywordCount' => $keywordsCount
        ];
        } catch (TransportExceptionInterface $e){
            return [];
        }
    }
}