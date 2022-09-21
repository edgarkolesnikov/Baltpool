<?php

namespace App\Tests;

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;

class UrlCheckerTest extends PantherTestCase
{

    //Checking /check page for status code and exact info on that page
    public function testPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/check');
        $this->assertSame(200, $client->getResponse()->getStatusCode() , 'response status is 2xx');
        $this->assertSelectorTextContains('h1', 'Check');
    }

    //Testing form submission, redirect, and link to come back to form page.
    public function testFormAndLink(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/check');
        $form = $crawler->selectButton('Submit')->form();
        $form['url_input_form[url]'] = "https://github.com/symfony/panther";
        $form['url_input_form[keyword]'] = 'Symfony';
        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertSame(200, $client->getResponse()->getStatusCode() , 'response status is 2xx');
        $this->assertSelectorTextContains('a', 'Grizti atgal');
        $link = $crawler->filter('a:contains("Grizti atgal")')->link();
        $crawler = $client->click($link);
    }
}
