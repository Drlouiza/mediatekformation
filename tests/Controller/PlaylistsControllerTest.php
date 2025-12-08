<?php

namespace App\tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PlaylistsControllerTest extends WebTestCase
{
    public function testAccesPage()
    {
        $client = static::createClient();
        $client->catchExceptions(false);
        $client->request('GET', '/playlists');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatuscode());
    }

    public function testContenuPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists');
        $this->assertSelectorTextContains('th', 'playlist');
        $this->assertCount(4, $crawler->filter('th'));
    }

    public function testLinkPlaylist()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists');

        // Cliquer sur le lien "Voir détail" de la première playlist
        $link = $crawler->filter('tbody tr:first-child td:last-child a')->attr('href');

        // Suivre le lien
        $crawler = $client->request('GET', $link);

        // Vérifie que la page existe
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // Vérifie que la route correspond bien
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $expectedUri = preg_replace('#.*/playlists/playlist/(\d+)#', '/playlists/playlist/$1', $link);
        $this->assertEquals($expectedUri, $uri);
    }

    public function testFiltrePlaylist()
    {
        $client = static::createClient();
        $client->request('GET', '/playlists');
        //simulation de la soumission du formulaire
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'Cours'
        ]);
        // vérifie le nombre de lignes obtenues
        $this->assertCount(11, $crawler->filter('h5'));
        // vérifie si la première playlist correspond à la recherche
        $this->assertSelectorTextContains('h5', 'Cours Composant logiciel');
    }

    public function testSortPlaylist()
    {
        $client = static::createClient();
        $client->request('GET', '/playlists/tri/name/DESC');
        $this->assertSelectorTextContains('h5', 'Visual Studio 2019 et C#');
        $client->request('GET', '/playlists/tri/nombre/ASC');
        $this->assertSelectorTextContains('h5', 'playlist test');
        $client->request('GET', '/playlists/tri/nombre/DESC');
        $this->assertSelectorTextContains('h5', 'Bases de la programmation (C#)');
    }
    

}