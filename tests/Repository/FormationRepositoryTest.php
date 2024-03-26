<?php

namespace App\tests\Repository;

use App\Entity\Formation;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FormationRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager; 
    private FormationRepository $formationRepository; 

    protected function setUp(): void
    {
        // Démarrage du noyau Symfony

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $this->formationRepository = $this->entityManager->getRepository(Formation::class);
    }

    // Teste le nombre total de formations dans la base de données
    public function testNbFormations()
    {
        $nbFormation = $this->formationRepository->count([]);

        $this->assertEquals(237, $nbFormation);
    }

    // Teste l'ajout d'une nouvelle formation
    public function testAddFormation()
    {
        // Récupère le nombre de formations avant l'ajout
        $nbFormationBefore = $this->formationRepository->count([]);

        // Crée une nouvelle formation
        $formation = new Formation();
        $formation->setTitle("Un titre")
            ->setDescription("Description blabla")
            ->setPublishedAt(new \DateTime("yesterday"));

        $this->entityManager->persist($formation);
        $this->entityManager->flush();

        $nbFormationAfter = $this->formationRepository->count([]);

        $this->assertEquals($nbFormationBefore + 1, $nbFormationAfter, "Erreur lors de l'ajout");
    }

    // Teste la suppression d'une formation
    public function testSupprFormation()
    {
        $nbFormationBefore = $this->formationRepository->count([]);

        $formation = $this->formationRepository->findOneBy(['title' => "Un titre"]);

        // Supprime la formation si elle existe
        if ($formation) {
            $this->entityManager->remove($formation);
            $this->entityManager->flush();
        }

        $nbFormationAfter = $this->formationRepository->count([]);

        // Vérifie que le nombre de formations a diminué de 1 après la suppression
        $this->assertEquals($nbFormationBefore - 1, $nbFormationAfter, "Erreur lors de la suppression");
    }
}
