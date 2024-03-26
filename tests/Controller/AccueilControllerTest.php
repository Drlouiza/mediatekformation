<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AccueilControllerTest extends WebTestCase {

   public function testAccesPage(){
       $client = static::createClient();
       $client->request('GET', '/');
       $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
   }
}

