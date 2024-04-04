<?php

namespace App\tests\Repository;

use App\Entity\Formation;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FormationRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private FormationRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$container->get(EntityManagerInterface::class);
        $this->repository = self::$container->get(FormationRepository::class);
    }

    public function testNbFormations(): void
    {
        $nbFormation = $this->repository->count([]);
        $this->assertEquals(237, $nbFormation);
    }

    private function createFormation(): Formation
    {
        return (new Formation())
            ->setTitle("Un titre")
            ->setDescription("Description blabla")
            ->setPublishedAt(new \DateTime("yesterday"));
    }

    public function testAddFormation(): void
    {
        $nbFormationBefore = $this->repository->count([]);
        
        $formation = $this->createFormation();
        $this->entityManager->persist($formation);
        $this->entityManager->flush();
        
        $nbFormationAfter = $this->repository->count([]);
        $this->assertEquals($nbFormationBefore + 1, $nbFormationAfter, "Erreur lors de l'ajout");
    }

    public function testSupprFormation(): void
    {
        $nbFormationBefore = $this->repository->count([]);
        $formation = $this->repository->findOneBy(['title' => "Un titre"]);

        $this->entityManager->remove($formation);
        $this->entityManager->flush();
        
        $nbFormationAfter = $this->repository->count([]);
        $this->assertEquals($nbFormationBefore - 1, $nbFormationAfter, "Erreur lors de la suppression");
    }
}
