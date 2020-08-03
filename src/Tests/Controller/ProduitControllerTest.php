<?php

namespace AcMarche\MaintenanceShop\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProduitControllerTest extends WebTestCase
{
    private $client;

    public function __construct()
    {
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'admin',
        ));
    }

    public function testIndex()
    {
        $crawler = $this->client->request('GET', '/produit/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAdd()
    {
        $crawler = $this->client->request('GET', '/produit/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(array(
            'produit[nom]' => 'Nettoyant vitres en vaporisateur',
        ));

        $option = $crawler->filter('#produit_categorie option:contains("Gros matériels")');
        $this->assertEquals(1, count($option), 'Gros matériels');
        $categorie = $option->attr('value');
        $form['produit[categorie]']->select($categorie);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Nettoyant vitres en vaporisateur")')->count());
        $this->assertGreaterThan(0, $crawler->filter('p:contains("Gros matériels")')->count());
    }

    public function testEdit()
    {
        $crawler = $this->client->request('GET', '/produit/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $formSearch = $crawler->selectButton('Rechercher')->form(array(
            'search_produit[nom]' => 'Nettoyant',
        ));

        $categorie_option = $crawler->filter('#search_produit_categorie option:contains("Gros matériels")');
        $this->assertGreaterThan(0, count($categorie_option), 'Gros matériels non trouvé');
        $categorie = $categorie_option->attr('value');
        $formSearch['search_produit[categorie]']->select($categorie);

        $crawler = $this->client->submit($formSearch);
//print_r($this->client->getResponse()->getContent());

        $crawler = $this->client->click($crawler->selectLink('Nettoyant vitres en vaporisateur')->link());
        $crawler = $this->client->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(array(
            'produit[nom]' => 'Nettoyant vitres en vaporisateurs',
        ));

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Nettoyant vitres en vaporisateurs")')->count());
    }

}