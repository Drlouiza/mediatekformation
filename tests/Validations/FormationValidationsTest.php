<?php
namespace App\Tests\Validations;

use App\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class FormationValidationsTest extends KernelTestCase {

    /**
     * Création d'un objet de type Formation, avec informations minimales
     * @return Formation
     */
    public function getFormation(): Formation{
        return (new Formation())
                ->setTitle("Formation test");
    }

    /**
     * Utilisaiton du Kernel pour tester une règle de validation
     * @param Formation $formation
     * @param int $nbErreursAttendues
     * @param string $message
     */
    public function assertErrors(Formation $formation, int $nbErreursAttendues, string $message=""){
        self::bootKernel();
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $error = $validator->validate($formation);
        if (count($error) !== $nbErreursAttendues) {
            foreach ($error as $e) {
                echo $e->getPropertyPath() . " : " . $e->getMessage() . "\n";
            }
        }
        $this->assertCount($nbErreursAttendues, $error, $message);

    }

    public function testValidDateFormation(){
        $this->assertErrors($this->getFormation()->setPublishedAt(new \DateTime('yesterday')), 0, " 04/10/2023 devrait réussir");
        $this->assertErrors($this->getFormation()->setPublishedAt(new \DateTime("first day of January 2008")), 0, " 01/01/2008 devrait réussir");
        $this->assertErrors($this->getFormation()->setPublishedAt(new \DateTime("last sat of July 2008")), 0, " 26/07/2008 devrait réussir");
    }
    
    
    public function testNonValidDateFormation(){
    $this->assertErrors($this->getFormation()->setPublishedAt(new \DateTime('2028-07-26')), 1, "devrait échouer");
    $this->assertErrors($this->getFormation()->setPublishedAt(new \DateTime('2029-08-24')), 1, "devrait échouer");
    $this->assertErrors($this->getFormation()->setPublishedAt(new \DateTime('2027-07-17')), 1, "devrait échouer");
    $this->assertErrors($this->getFormation()->setPublishedAt(new \DateTime('2028-05-16')), 1, "devrait échouer");
    
}

}
