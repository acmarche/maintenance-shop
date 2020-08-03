<?php

namespace AcMarche\MaintenanceShop\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommandeControllerTest extends WebTestCase
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
        $crawler = $this->client->request('GET', '/commande/');

        $formSearch = $crawler->selectButton('Rechercher')->form(array(
            'search_produit[nom]' => 'Nettoyant',
        ));

        $categorie_option = $crawler->filter('#search_produit_categorie option:contains("Gros matériels")');
        $this->assertGreaterThan(0, count($categorie_option), 'Gros matériels non trouvé');
        $categorie = $categorie_option->attr('value');
        $formSearch['search_produit[categorie]']->select($categorie);

        $crawler = $this->client->submit($formSearch);

        $this->assertGreaterThan(0, $crawler->filter('li:contains("Nettoyant vitres en vaporisateurs")')->count());

    }
}
