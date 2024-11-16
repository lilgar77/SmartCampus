<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FloorTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/floor');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des Etages');
        $this->assertCount(0, $crawler->filter('div.room')); // Assure qu'il n'y a pas encore de chambre, ou ajustez selon les données
    }

    public function testAddRoom()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/floor/add');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Ajouter un Etage');

        // Remplissage du formulaire pour ajouter un etage
        $form = $crawler->selectButton('Ajouter un Etage')->form([
            'floor[numberFloor]' => '5',
            'floor[IdBuilding]' => '1', // Batiment
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/floor');
        $client->followRedirect();

        // Vérification que l'étage a été ajoutée dans la liste
        $this->assertSelectorTextContains('tr:nth-child(5) td:nth-child(2)', '5');
        $this->assertSelectorTextContains('tr:nth-child(5) td:nth-child(3)', 'Informatique');
    }
    public function testEditRoom()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/floor/2/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Modifier l\'étage "1"');

        // Remplissage du formulaire pour ajouter un etage
        $form = $crawler->selectButton('Sauvegarder les modifications')->form([
            'floor[numberFloor]' => '7',
            'floor[IdBuilding]' => '1',
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/floor');
        $client->followRedirect();

        // Vérification que l'étage a été ajoutée dans la liste
        $this->assertSelectorTextContains('tr:nth-child(2) td:nth-child(2)', '7');
        $this->assertSelectorTextContains('tr:nth-child(2) td:nth-child(3)', 'Informatique');
    }

    public function testDeleteRoom()
    {
        $client = static::createClient();

        // Ajouter un étage avec un ID spécifique (par exemple, ID 2) dans la base de données.
        // Tu peux t'assurer qu'il existe, ou utiliser des fixtures pour remplir la base de données
        // si ce n'est pas déjà fait.

        // Si tu veux garantir qu'un étage spécifique existe avant de le supprimer, tu peux
        // récupérer l'ID d'un étage dans la base de données ou créer un étage dans une fixture
        // pour qu'il soit présent avant de commencer les tests.

        // Par exemple, récupérer l'étage avec l'ID 2
        $crawler = $client->request('GET', '/floor');

        // Vérifie que la page de la liste des étages est bien chargée
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des Etages');

        // Vérifie que l'étage avec l'ID 2 est présent
        $this->assertSelectorTextContains('tr:nth-child(2) td:nth-child(1)', '2');  // Ici l'ID de l'étage

        $client->request('POST', '/floor/1'); // Suppression sans CSRF

        // Vérifier la redirection après suppression
        $this->assertResponseRedirects('/floor');

        // Suivre la redirection
        $client->followRedirect();
        // Vérifie que l'étage a bien été supprimé en recherchant son ID
        $this->assertSelectorTextContains('tr:nth-child(2) td:nth-child(1)','3'); // L'ID 2 ne doit plus être présent dans la liste des étages
    }

}
