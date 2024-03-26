<?php

namespace App\tests\Repository;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategorieRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private CategorieRepository $categorieRepository;

    protected function setUp(): void
    {
        // Démarrage du noyau Symfony

        $kernel = self::bootKernel();

        // Récupération de l'EntityManager et du Repository de Categorie
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->categorieRepository = $this->entityManager->getRepository(Categorie::class);
    }

    // Teste le nombre de catégories
    public function testNbCategorie()
    {
        $nbCategorie = $this->categorieRepository->count([]);
        $this->assertEquals(9, $nbCategorie);
    }

    // Crée une nouvelle catégorie
    public function newCategorie(): Categorie
    {
        $categorie = (new Categorie())
            ->setName("Un nom");
        return $categorie;
    }

    // Teste l'ajout d'une catégorie
    public function testAddCategorie()
    {
        $nbCategorieBefore = $this->categorieRepository->count([]);

        $categorie = $this->newCategorie();
        $this->categorieRepository->add($categorie, true);

        $nbCategorieAfter = $this->categorieRepository->count([]);
        
        // Vérifie si le nombre de catégories a augmenté après l'ajout de la nouvelle catégorie
        $this->assertEquals($nbCategorieBefore + 1, $nbCategorieAfter, "Erreur lors de l'ajout de la catégorie");
    }

    // Teste la suppression d'une catégorie
    public function testSupprCategorie()
    {
        $nbCategorieBefore = $this->categorieRepository->count([]);
        $categorie = $this->categorieRepository->findOneBy(['name' => "Un nom"]);

        if ($categorie) {
            $this->categorieRepository->remove($categorie, true);
        }

        $nbCategorieAfter = $this->categorieRepository->count([]);
        
        // Vérifie si le nombre de catégories a diminué après la suppression de la catégorie
        $this->assertEquals($nbCategorieBefore - 1, $nbCategorieAfter, "Erreur lors de la suppression de la catégorie");
    }
}

