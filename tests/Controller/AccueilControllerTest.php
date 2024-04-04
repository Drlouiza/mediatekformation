<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AccueilControllerTest extends WebTestCase
{
    public function testAccesPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
    }
}
