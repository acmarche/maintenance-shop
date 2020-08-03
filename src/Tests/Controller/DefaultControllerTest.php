<?php

namespace AcMarche\MaintenanceShop\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertContains('Pour effectuer votre commande', $client->getResponse()->getContent());
    }
}
