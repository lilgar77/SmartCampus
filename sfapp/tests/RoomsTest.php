<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RoomsTest extends WebTestCase
{
    public function testIndexPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/rooms');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des Salles');
        $this->assertCount(0, $crawler->filter('div.room')); // Assure qu'il n'y a pas encore de chambre, ou ajustez selon les données
    }

    public function testAddRoom()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/rooms/add');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Ajouter une Salle');

        // Remplissage du formulaire pour ajouter une salle
        $form = $crawler->selectButton('Ajouter la Salle')->form([
            'roomForm[name]' => 'Test Room',
            'roomForm[id_AS]' => '1', // ID de l'AS
            'roomForm[floor]' => '3', // Etage
            'roomForm[building]' => 'Building A', // Batiment
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/rooms');
        $client->followRedirect();

        // Vérification que la salle a été ajoutée dans la liste
        $this->assertSelectorTextContains('div.room', 'Test Room');
        $this->assertSelectorTextContains('div.room', 'Building A');
        $this->assertSelectorTextContains('div.room', '3'); // Vérifie l'étage
    }

    public function testEditRoom()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/rooms/1/edit'); // Assurez-vous que la chambre avec l'ID 1 existe

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Edit Room');

        // Remplissage du formulaire pour modifier une chambre existante
        $form = $crawler->selectButton('Save')->form([
            'roomForm[name]' => 'Updated Room',
            'roomForm[capacity]' => 4,
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/rooms');
        $client->followRedirect();

        // Vérification que le nom de la chambre a été mis à jour
        $this->assertSelectorTextContains('div.room', 'Updated Room');
    }

    public function testDeleteRoom()
    {
        $client = static::createClient();
        $client->request('POST', '/rooms/1'); // Supposons que la chambre avec l'ID 1 existe

        $this->assertResponseRedirects('/rooms');
        $client->followRedirect();

        // Vérification que la chambre a été supprimée
        $client->request('GET', '/rooms');
        $this->assertSelectorNotContains('div.room', 'Room to Delete'); // Assurez-vous que la chambre supprimée n'est plus visible
    }

    public function testSearchRoom()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/rooms');

        // Soumettre une recherche par nom de chambre
        $form = $crawler->selectButton('Search')->form([
            'search_room_form[name]' => 'Test Room', // Assurez-vous que cette chambre existe dans la base de données
        ]);

        $client->submit($form);
        $this->assertResponseIsSuccessful();

        // Vérification que la chambre correspondante est affichée
        $this->assertSelectorTextContains('div.room', 'Test Room');
    }
}
