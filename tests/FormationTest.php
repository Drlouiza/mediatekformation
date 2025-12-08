<?php

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

class FormationTest extends TestCase{
    public function testGetPublishedAtString()
    {
        $formation = new formation();
        $formation->setTitle("Formation test");
        $formation->setPublishedAt(new \DateTime("2025-12-01"));
        $this->assertEquals("01/12/2025", $formation->getPublishedAtString());
    }

    public function testGetPublishedAtStringWhenNull() {
    $formation = new Formation();
    $formation->setPublishedAt(null);
    $this->assertEquals("", $formation->getPublishedAtString());
}
}


