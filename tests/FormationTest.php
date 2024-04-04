<?php

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;


class FormationTest extends TestCase
{
    public function testGetPublishedAtString() 
    {
        $Formation = new Formation();
        $Formation->setTitle("Formation test");
        $Formation->setPublishedAt(new \DateTime("2022-05-17"));
        $this->assertEquals("17/05/2022", $Formation->getPublishedAtString());
    }
}