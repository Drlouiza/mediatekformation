<?php

namespace App\Tests\Validations\Repository;

use App\Entity\Playlist;
use Doctrine\ORM\EntityManager;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlaylistRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function recupRepository(): PlaylistRepository
    {
        
        return $this->entityManager->getRepository(Playlist::class);
    }

    public function testNbPlaylist()
    {
        $repository = $this->recupRepository();
        $nbPlaylist = $repository->count([]);
        $this->assertEquals(28, $nbPlaylist);
    }

    public function newPlaylist(): Playlist
    {
        $playlist = (new Playlist())
            ->setName("Un Nom");
        return $playlist;
    }

    public function testAddPlaylist()
    {
        $repository = $this->recupRepository();
        $playlist = $this->newPlaylist();
        $nbPlaylist = $repository->count([]);

        $this->entityManager->persist($playlist);
        $this->entityManager->flush();
        $this->assertEquals($nbPlaylist + 1, $repository->count([]), "erreur lors de l'ajout");
    }

    public function testSupprPlaylist()
    {
        $repository = $this->recupRepository();

        $nbPlaylist = $repository->count([]);
        $playlist = $repository->findOneBy(['name' => "Un Nom"]);
        
        
        $this->entityManager->remove($playlist);
        $this->entityManager->flush();
        $this->assertEquals($nbPlaylist - 1, $repository->count([]), "erreur lors de la suppression");
    }
}