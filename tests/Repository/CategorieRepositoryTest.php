<?php

namespace App\Tests\Validations\Repository;

use App\Entity\Categorie;
use Doctrine\ORM\EntityManager;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategorieRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function recupRepository(): CategorieRepository
    {
        
        return $this->entityManager->getRepository(Categorie::class);
    }

    public function testNbCategorie()
    {
        $repository = $this->recupRepository();
        $nbCategorie = $repository->count([]);
        $this->assertEquals(9, $nbCategorie);
    }

    public function newCategorie(): Categorie
    {
        $categorie = (new categorie())
            ->setName("Un Nom test");
        return $categorie;
    }

    public function testAddCategorie()
    {
        $repository = $this->recupRepository();
        $categorie = $this->newCategorie();
        $nbCategorie = $repository->count([]);

        $this->entityManager->persist($categorie);
        $this->entityManager->flush();
        $this->assertEquals($nbCategorie + 1, $repository->count([]), "erreur lors de l'ajout");
    }

    public function testSupprCategorie()
    {
        $repository = $this->recupRepository();

        $nbCategorie = $repository->count([]);
        $categorie = $repository->findOneBy(['name' => "Un Nom test"]);
        
        
        $this->entityManager->remove($categorie);
        $this->entityManager->flush();
        $this->assertEquals($nbCategorie - 1, $repository->count([]), "erreur lors de la suppression");
    }

}