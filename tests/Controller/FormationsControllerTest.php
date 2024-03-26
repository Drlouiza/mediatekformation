<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FormationsControllerTest extends WebTestCase
{
    private const FORMATIONSPATH = '/formations';

    public function testAccesPage()
    {
        $client = static::createClient();
        $client->request('GET', self::FORMATIONSPATH);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testTriPlaylistsAsc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/name/ASC/playlist');
        $this->assertSelectorTextContains('th', 'formation');
        $this->assertCount(5, $crawler->filter('th'));
        $this->assertSelectorTextContains('h5', 'Bases de la programmation n°74 - POO : collections');
    }
    
    public function testTriFormationsAsc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/title/ASC');
        $this->assertSelectorTextContains('th', 'formation');
        $this->assertCount(5, $crawler->filter('th'));
        $this->assertSelectorTextContains('h5', 'Android Studio (complément n°1) : Navigation Drawer et Fragment');
    }
    
    public function testTriDateAsc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/publishedAt/ASC');
        $this->assertSelectorTextContains('th', 'formation');
        $this->assertCount(5, $crawler->filter('th'));
        $this->assertSelectorTextContains('h5', 'Cours UML (1 à 7 / 33) : introduction');
    }
    
     public function testFiltreFormations()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations');
        $form = $crawler->selectButton('filtrer')->form();
        $form['recherche'] = 'UML';
        $client->submit($form);

        $this->assertStringContainsString('Cours UML', $crawler->filter('table')->text());
    }
    
    public function testFiltrePlaylists()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/recherche/name/playlist');
        $form = $crawler->filter('form')->form();
        $form['recherche'] = 'Eclipse';
        $client->submit($form);
        $this->assertCount(9, $crawler->filter('h5:contains("Eclipse")'));
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
}




