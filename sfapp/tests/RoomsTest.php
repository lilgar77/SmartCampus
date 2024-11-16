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
            'room_form[id_AS]' => '10',  // Ensure this is a valid AS ID in your test database
            'room_form[floor]' => '9',  // Ensure this is a valid floor ID
            'room_form[building]' => '5', // Ensure this is a valid building ID
        ]);

        // Confirm redirection after form submission
        $this->assertResponseRedirects('/rooms');
        $client->followRedirect();

        // Verify that the new room is in the list
        $this->assertSelectorExists('td');// Ensure the table has at least one row
        $this->assertSelectorTextContains('tr:nth-child(2) td:nth-child(2)', 'Test Room');
        $this->assertSelectorTextContains('tr:nth-child(2) td:nth-child(3)', '0');
        $this->assertSelectorTextContains('tr:nth-child(2) td:nth-child(4)', 'Informatique');

    }

    public function testSearchRoom()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/rooms');

        $this->assertSelectorExists('form[name="search_room_form"]');
        // Soumettre une recherche par nom de salle
        $client->submitForm('Rechercher', [
            'search_room_form[name]' => 'D302',
             // Ensure this is a valid building ID
        ]);
        $this->assertResponseIsSuccessful();

        // Vérification que la salle correspondante est affichée
        $this->assertSelectorExists('td');// Ensure the table has at least one row
        $this->assertSelectorTextContains('td:nth-child(2)', 'D302');
        $this->assertSelectorTextContains('td:nth-child(3)', '3');
        $this->assertSelectorTextContains('td:nth-child(4)', 'Informatique');
    }
}