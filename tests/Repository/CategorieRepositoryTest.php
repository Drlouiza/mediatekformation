<?php

namespace App\tests\Repository;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategorieRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private CategorieRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$container->get(EntityManagerInterface::class);
        $this->repository = self::$container->get(CategorieRepository::class);
    }

    public function testNbCategorie(): void
    {
        $nbCategorie = $this->repository->count([]);
        $this->assertEquals(9, $nbCategorie);
    }

    private function createCategorie(): Categorie
    {
        return (new Categorie())
            ->setName("Un nom");
    }

    public function testAddCategorie(): void
    {
        $nbCategorieBefore = $this->repository->count([]);
        
        $categorie = $this->createCategorie();
        $this->entityManager->persist($categorie);
        $this->entityManager->flush();
        
        $nbCategorieAfter = $this->repository->count([]);
        $this->assertEquals($nbCategorieBefore + 1, $nbCategorieAfter, "Erreur lors de l'ajout");
    }

    public function testSupprCategorie(): void
    {
        $nbCategorieBefore = $this->repository->count([]);
        $categorie = $this->repository->findOneBy(['name' => "Un nom"]);

        $this->entityManager->remove($categorie);
        $this->entityManager->flush();
        
        $nbCategorieAfter = $this->repository->count([]);
        $this->assertEquals($nbCategorieBefore - 1, $nbCategorieAfter, "Erreur lors de la suppression");
    }
}
