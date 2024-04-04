<?php
namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PlaylistsControllerTest extends WebTestCase
{
    public function testAccessPage()
    {
        $client = static::createClient();
        $client->request('GET', '/playlists');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testContentPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists');
        $this->assertSelectorTextContains('th', 'playlist');
        $this->assertCount(4, $crawler->filter('th'));
    }

        public function testTriPlaylists()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/name/ASC');

        // Vérifiez que la réponse est OK
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // Vérifiez la présence du tableau avec la classe "table-striped"
        $this->assertGreaterThan(
            0,
            $crawler->filter('table.table-striped')->count(),
            'La page ne contient pas de tableau avec la classe "table-striped"'
        );

        // Vérifiez la présence des titres de playlist
        $this->assertGreaterThan(
            0,
            $crawler->filter('table.table-striped td h5.text-info')->count(),
            'La page ne contient pas de titre de playlist'
        );

        // Vérifiez la présence des nombres de formations
        $this->assertGreaterThan(
            0,
            $crawler->filter('table.table-striped td h5.text-center')->count(),
            'La page ne contient pas de nombre de formations'
        );

        // Vérifiez la présence des liens de détail
        $this->assertGreaterThan(
            0,
            $crawler->filter('table.table-striped td.text-center a.btn')->count(),
            'La page ne contient pas de lien de détail'
        );
    }
    
    public function testTriNbFormations()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/nombre/ASC');

        // Vérifiez que la réponse est OK
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // Vérifiez la présence de la playlist "Cours Informatique embarquée"
        $this->assertGreaterThan(
            0,
            $crawler->filter('table.table-striped td h5.text-info:contains("Cours Informatique embarquée")')->count(),
            'La playlist "Cours Informatique embarquée" n\'a pas été trouvée'
        );

        // Vérifiez que le tableau contient au moins 4 colonnes
        $this->assertGreaterThanOrEqual(
            4,
            $crawler->filter('table.table-striped th')->count(),
            'Le tableau ne contient pas au moins 4 colonnes'
        );
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

    public function testFilterPlaylist()
    {
        $client = static::createClient();
        $client->request('GET', '/playlists');
        // Simulation de la soumission du formulaire
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'Cours'
        ]);
        // Vérifie le nombre de lignes obtenues
        $this->assertCount(22, $crawler->filter('h5'));
        // Vérifie si la première playlist correspond à la recherche
        $this->assertSelectorTextContains('h5', 'Cours Composant logiciel');
    }

    public function testSortPlaylist()
    {
        $client = static::createClient();
        $client->request('GET', '/playlists/tri/name/DESC');
        $this->assertSelectorTextContains('h5', 'Visual Studio 2019 et C#');
        $client->request('GET', '/playlists/tri/nombre/ASC');
        $this->assertSelectorTextContains('h5', 'Cours Informatique embarquée');
        $client->request('GET', '/playlists/tri/nombre/DESC');
        $this->assertSelectorTextContains('h5', 'Bases de la programmation (C#)');
    }
    
    

}
