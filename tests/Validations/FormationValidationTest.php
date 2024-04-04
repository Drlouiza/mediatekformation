<?php
namespace App\Tests\Validations;

use App\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 *Assurez-vous que la date de la formation n'est pas ultérieure à la date actuelle lors de l'ajout ou de la modification d'une formation.
 */

class FormationValidationsTest extends KernelTestCase {

    /**
     * @return Formation
     */
    public function getFormation(): 
        Formation
        {
        return (new Formation())
                ->setTitle("Formation test");
        }

    /**
     * @param Formation $formation
     * @param int $nbErreursAttendues
     * @param string $message
     */
    public function assertErrors(Formation $formation, int $nbErreursAttendues, string $message=""){
        self::bootKernel();
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $error = $validator->validate($formation);
        $this->assertCount($nbErreursAttendues, $error, $message);
    }

    public function testValidDateFormation(){
        $this->assertErrors($this->getFormation()->setPublishedAt(new \DateTime('yesterday')), 0, " 04/10/2023 devrait réussir");
        $this->assertErrors($this->getFormation()->setPublishedAt(new \DateTime("first day of January 2008")), 0, " 01/01/2008 devrait réussir");
        $this->assertErrors($this->getFormation()->setPublishedAt(new \DateTime("last sat of July 2008")), 0, " 26/07/2008 devrait réussir");
    }
    
    public function testNonValidDateFormation(){
        $this->assertErrors($this->getFormation()->setPublishedAt(new \DateTime('06/08/2026')), 1, " devrait échouer");
        $this->assertErrors($this->getFormation()->setPublishedAt(new \DateTime('11/02/2025')), 1, " devrait échouer");
        $this->assertErrors($this->getFormation()->setPublishedAt(new \DateTime('06/11/2028')), 1, " devrait échouer");
        $this->assertErrors($this->getFormation()->setPublishedAt(new \DateTime('09/07/2025')), 1, " devrait échouer");
    }
    
}