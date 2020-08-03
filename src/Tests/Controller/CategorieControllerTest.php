<?php

namespace AcMarche\MaintenanceShop\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategorieControllerTest extends WebTestCase {

    private $client;

    public function __construct() {
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'admin',
        ));
    }

    public function testIndex() {

        $crawler = $this->client->request('GET', '/categorie/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAdd() {

        $crawler = $this->client->request('GET', '/categorie/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(array(
            'categorie[nom]' => 'Gros matériel',
        ));

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Gros matériel")')->count());
    }

    public function testEdit() {

        $crawler = $this->client->request('GET', '/categorie/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->click($crawler->selectLink('Gros matériel')->link());

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Gros matériel")')->count());

        $crawler = $this->client->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(array(
            'categorie[nom]' => 'Gros matériels',
        ));

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Gros matériels")')->count());
    }

}