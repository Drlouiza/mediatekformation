<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FormationsControllerTest extends WebTestCase
{
    public function testAccessPage(): void
    {
        $client = static::createClient();
        $client->catchExceptions(false);
        
        $client->request('GET', '/formations');
        $response = $client->getResponse();
        
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testLinkFormations()
    {
        $client = static::createClient();
        $client->request('GET', '/formations');
        $crawler = $client->getCrawler();
        $link = $crawler->selectLink('image miniature')->link();
        $client->click($link);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $uri = $client->getRequest()->getUri();
        $this->assertStringContainsString('/formations/formation/', $uri);
    }

    public function testFilterFormation(): void
    {
        $client = static::createClient();
        $client->request('GET', '/formations');
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'Eclipse n°3 : GitHub et Eclipse'
        ]);
        
        // Vérifie le nombre de lignes obtenues
        $this->assertCount(1, $crawler->filter('h5'));
        
        $this->assertSelectorTextContains('h5', 'Eclipse n°3 : GitHub et Eclipse');
    }
    
    public function testSortFormation(): void
    {
        $client = static::createClient();
        $client->request('GET', '/formations/tri/title/DESC');
        
        $this->assertSelectorTextContains('h5', 'UML : Diagramme de paquetages');
        
        $client->request('GET', '/formations/tri/name/DESC/playlist');
        
        $this->assertSelectorTextContains('h5', 'C# : ListBox en couleur');
        
        $client->request('GET', '/formations/tri/publishedAt/ASC');
        
        $this->assertSelectorTextContains('h5', 'Cours UML (1 à 7 / 33) : introduction et cas d\'utilisation');
    }
    
    public function testContentPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations');
        
        $this->assertSelectorTextContains('th', 'formation');
        $this->assertCount(5, $crawler->filter('th'));
    }
}

