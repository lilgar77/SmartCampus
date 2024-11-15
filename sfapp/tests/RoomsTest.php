<?php

namespace App\Tests;

use App\Model\EtatAS;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Room;
use App\Entity\Floor;
use App\Entity\AcquisitionSystem;
use App\Entity\Building;

class RoomsControllerTest extends WebTestCase
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

        // Check if the page loaded successfully
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="room_form"]');

        // Submit the form with test values
        $client->submitForm('Ajouter une Salle', [
            'room_form[name]' => 'Test Room',
            'room_form[id_AS]' => '6',  // Replace with a valid ID_AS
            'room_form[floor]' => '1',                  // Replace with a valid floor ID
            'room_form[building]' => '1',    // Replace with a valid building ID
        ]);

        // Confirm redirection after form submission
        $this->assertResponseRedirects('/rooms');
        $client->followRedirect();


        // Vérification que la salle a bien été ajoutée dans la liste
        $this->assertSelectorExists('td');  // Vérifie si une ligne de tableau existe
        $this->assertSelectorTextContains('td:nth-child(2)', 'Test Room');
        
        // Vérifie si le nom de la salle est dans une ligne
        // Vérification de l'étage
    }

    /*public function testEditRoom()
    {
        $client = static::createClient();
        // Assurez-vous que la salle avec l'ID 1 existe dans la base de données
        $crawler = $client->request('GET', '/rooms/1/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Modifier la Salle');

        // Vérification des valeurs initiales avant modification
        $this->assertSelectorTextContains('input[name="roomForm[name]"][value="Test Room"]');
        $this->assertSelectorTextContains('input[name="roomForm[id_AS]"][value="1"]');
        $this->assertSelectorTextContains('input[name="roomForm[floor]"][value="1"]');
        $this->assertSelectorTextContains('input[name="roomForm[building]"][value="1"]');

        // Remplissage du formulaire pour modifier la salle
        $form = $crawler->selectButton('Sauvegarder les modifications')->form([
            'roomForm[name]' => 'Updated Room',
            'roomForm[id_AS]' => '2', // Nouveau ID pour Système d'Acquisition
            'roomForm[floor]' => '2', // Nouvel ID pour l'étage
            'roomForm[building]' => '2', // Nouveau ID pour le bâtiment
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/rooms');
        $client->followRedirect();

        // Vérification que la salle a bien été mise à jour
        $this->assertSelectorTextContains('div.room', 'Updated Room');
        $this->assertSelectorTextContains('div.room', 'Building B'); // Vérification du bâtiment mis à jour
        $this->assertSelectorTextContains('div.room', '2'); // Vérification de l'étage mis à jour
    }

    public function testDeleteRoom()
    {
        $client = static::createClient();
        $client->request('POST', '/rooms/1'); // Supposons que la salle avec l'ID 1 existe et est supprimée

        $this->assertResponseRedirects('/rooms');
        $client->followRedirect();

        // Vérification que la salle a été supprimée
        $client->request('GET', '/rooms');
        $this->assertSelectorNotContains('div.room', 'Room to Delete'); // Assurez-vous que la salle supprimée n'est plus visible
    }

    public function testSearchRoom()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/rooms');

        // Soumettre une recherche par nom de salle
        $form = $crawler->selectButton('Search')->form([
            'search_room_form[name]' => 'Test Room', // Assurez-vous que cette salle existe dans la base de données
        ]);

        $client->submit($form);
        $this->assertResponseIsSuccessful();

        // Vérification que la salle correspondante est affichée
        $this->assertSelectorTextContains('div.room', 'Test Room');
    }*/
}