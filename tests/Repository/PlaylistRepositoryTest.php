<?php

namespace App\tests\Repository;

use App\Entity\Playlist;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlaylistRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::$container->get('doctrine')->getManager();
    }

    public function recupRepository(): PlaylistRepository
    {
        return self::$container->get(PlaylistRepository::class);
    }

    public function testNbPlaylist(): void
    {
        $repository = $this->recupRepository();
        $nbPlaylist = $repository->count([]);
        $this->assertEquals(27, $nbPlaylist);
    }

    public function newPlaylist(): Playlist
    {
        $playlist = (new Playlist())
            ->setName("Un nom");
        return $playlist;
    }

    public function testAddPlaylist(): void
    {
        $repository = $this->recupRepository();
        $playlist = $this->newPlaylist();
        $nbPlaylist = $repository->count([]);

        $this->entityManager->persist($playlist);
        $this->entityManager->flush();
        $this->assertEquals($nbPlaylist + 1, $repository->count([]), "erreur lors de l'ajout");
    }

    public function testSupprPlaylist(): void
    {
        $repository = $this->recupRepository();

        $nbPlaylist = $repository->count([]);
        $playlist = $repository->findOneBy(['name' => "Un nom"]);
        $this->entityManager->remove($playlist);
        $this->entityManager->flush();
        $this->assertEquals($nbPlaylist - 1, $repository->count([]), "erreur lors de la suppression");
    }
}
