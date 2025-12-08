<?php

namespace App\tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccueilControllerTest extends WebTestCase
{
    public function testAccesPage()
    {
        $client = static::createClient();
        $client->catchExceptions(false);
        $client->request('GET', '/');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatuscode());
    }

    public function testContenuPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertSelectorTextContains('h3', 'MediaTek86');
        $this->assertSelectorTextContains('h5', 'Eclipse n°8');
        $this->assertCount(2, $crawler->filter('h5'));
    }

    public function testLinkFormation()
    {
        $client = static::createClient();
        $crawler =  $client->request('GET', '/');
        //click sur un lien ( le nom d'une ville)
        $link = $crawler->filter('a[href*="/formations/formation/"]')->first()->attr('href');
        $crawler = $client->request('GET', $link);
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        // Vérifie que l'URL contient bien /formations/formation/
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertStringContainsString('/formations/formation/', $uri);
    }

}