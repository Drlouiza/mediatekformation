<?php

namespace App\tests\Repository;

use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlaylistRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private PlaylistRepository $playlistRepository;

    protected function setUp(): void
    {
        // Démarrage du noyau Symfony
        $kernel = self::bootKernel();

        // Récupération de l'EntityManager et du Repository de Playlist
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->playlistRepository = $this->entityManager->getRepository(Playlist::class);
    }

    // Teste le nombre de playlists
    public function testNbPlaylist()
    {
        $nbPlaylist = $this->playlistRepository->count([]);
        $this->assertEquals(27, $nbPlaylist);
    }

    // Crée une nouvelle playlist
    public function newPlaylist(): Playlist
    {
        $playlist = (new Playlist())
            ->setName("Un nom");
        return $playlist;
    }

    // Teste l'ajout d'une playlist
    public function testAddPlaylist()
    {
        $nbPlaylistBefore = $this->playlistRepository->count([]);

        $playlist = $this->newPlaylist();
        $this->playlistRepository->add($playlist, true);

        $nbPlaylistAfter = $this->playlistRepository->count([]);
        $this->assertEquals($nbPlaylistBefore + 1, $nbPlaylistAfter, "Erreur lors de l'ajout de la playlist");
    }

    // Teste la suppression d'une playlist
    public function testSupprPlaylist()
    {
        $nbPlaylistBefore = $this->playlistRepository->count([]);
        $playlist = $this->playlistRepository->findOneBy(['name' => "Un nom"]);

        if ($playlist) {
            $this->playlistRepository->remove($playlist, true);
        }

        $nbPlaylistAfter = $this->playlistRepository->count([]);
        $this->assertEquals($nbPlaylistBefore - 1, $nbPlaylistAfter, "Erreur lors de la suppression de la playlist");
    }
}

