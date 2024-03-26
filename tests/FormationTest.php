<?php


use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires sur la date de parution au format string
 */
class FormationTest extends TestCase {

    public function testGetPublishedAtString(){
       $formation = new Formation();
       $formation->setPublishedAt(new DateTime("2022-04-14"));
       $this->assertEquals("14/04/2022", $formation->getPublishedAtString());
    }
}