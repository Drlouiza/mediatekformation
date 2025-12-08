<?php

namespace App\Tests\Validations\Repository;

use App\Entity\Formation;
use Doctrine\ORM\EntityManager;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FormationRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function recupRepository(): FormationRepository
    {
        
        return $this->entityManager->getRepository(Formation::class);
    }

    public function testNbFormations()
    {
        $repository = $this->recupRepository();
        $nbFormation = $repository->count([]);
        $this->assertEquals(237, $nbFormation);
    }

    public function newFormation(): Formation
    {
        $formation = (new Formation())
            ->setTitle("Un titre")
            ->setDescription("description test")
            ->setPublishedAt(new \DateTime("yesterday"));
        return $formation;
    }

    public function testAddFormation()
    {
        $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $nbFormation = $repository->count([]);

        $this->entityManager->persist($formation);
        $this->entityManager->flush();
        $this->assertEquals($nbFormation + 1, $repository->count([]), "erreur lors de l'ajout");
    }

    public function testSupprFormation()
    {
        $repository = $this->recupRepository();

        $nbFormation = $repository->count([]);
        $formation = $repository->findOneBy(['title' => "Un titre"]);
        
        
        $this->entityManager->remove($formation);
        $this->entityManager->flush();
        $this->assertEquals($nbFormation - 1, $repository->count([]), "erreur lors de la suppression");
    }
}
