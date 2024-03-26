<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PlaylistsControllerTest extends WebTestCase {

    public function testAccesPage(){
        $client = static::createClient();
        $client->request('GET', '/playlists');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testTriPlaylists()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'playlists/tri/name/ASC');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0,
            $crawler->filter('table.table-striped')->count(),
            'La page ne contient pas de tableau avec la classe "table-striped"'
        );
        
        $this->assertGreaterThan(
            0,
            $crawler->filter('table.table-striped td h5.text-info')->count(),
            'La page ne contient pas de titre de playlist'
        );

        $this->assertGreaterThan(
            0,
            $crawler->filter('table.table-striped td h5.text-center')->count(),
            'La page ne contient pas de nombre de formations'
        );

        $this->assertGreaterThan(
            0,
            $crawler->filter('table.table-striped td.text-center a.btn')->count(),
            'La page ne contient pas de lien de détail'
        );
    }

    public function testTriNbFormations()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/nbformations/ASC');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->assertGreaterThan(
            0,
            $crawler->filter('table.table-striped td h5.text-info:contains("Cours Informatique embarquée")')->count(),
            'La playlist "Cours Informatique embarquée" n\'a pas été trouvée'
        );

        $this->assertGreaterThanOrEqual(
            4,
            $crawler->filter('table.table-striped th')->count(),
            'Le tableau ne contient pas au moins 4 colonnes'
        );
    }
    
    public function testFiltrePlaylists()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/recherche/name');
        $form = $crawler->filter('form[action="/playlists/recherche/name"]')->form();
        $crawler = $client->submit($form, [
            'recherche' => 'sujet'
        ]);
        $this->assertCount(8, $crawler->filter('table.table-striped td h5.text-info'));
    }

   
    public function testFiltreCategories()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/recherche/id/categories');
        $form = $crawler->filter('form[action="/playlists/recherche/id/categories"]')->form();

        // Soumettre le formulaire avec la valeur "UML" pour la catégorie
        $crawler = $client->submit($form, [
            'recherche' => '2'
        ]);
        $this->assertCount(2, $crawler->filter('table.table-striped td h5.text-info'));
    }

    public function testLinkPlaylists() {
        $client = static::createClient();
        $client->request('GET','/playlists');
        $client->clickLink("Voir détail");
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertEquals('/playlists/playlist/13', $uri);
    }
    



}


