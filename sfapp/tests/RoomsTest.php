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
            'room_form[id_AS]' => '7',  // Ensure this is a valid AS ID in your test database
            'room_form[floor]' => '5',  // Ensure this is a valid floor ID
            'room_form[building]' => '3', // Ensure this is a valid building ID
        ]);

        // Confirm redirection after form submission
        $this->assertResponseRedirects('/rooms');
        $client->followRedirect();

        // Verify that the new room is in the list
        $this->assertSelectorExists('td'); // Ensure the table has at least one row
        $this->assertSelectorTextContains('td:nth-child(2)', 'Test Room');
        $this->assertSelectorTextContains('td:nth-child(3)', '0');
        $this->assertSelectorTextContains('td:nth-child(4)', 'Informatique');

    }

    public function testEditRoom()
    {
        $client = static::createClient();
        $entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();


        // Ensure the room exists in the database
        $room = $entityManager->getRepository(Room::class)->find(1);
        if (!$room) {
            $room = new Room();
            $room->setName('Test Room');
            $room->setIdAS($entityManager->getRepository(AcquisitionSystem::class)->find(7));
            $room->setFloor($entityManager->getRepository(Floor::class)->find(5));
            $room->setBuilding($entityManager->getRepository(Building::class)->find(3));
            $entityManager->persist($room);
            $entityManager->flush();
        }

        $crawler = $client->request('GET', '/rooms/1/edit');



        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Modifier la Salle "Test Room"');

        // Vérification des valeurs initiales avant modification
        $this->assertSelectorTextContains('input[name="room_Form[name]"][value="Test Room"]');
        $this->assertSelectorTextContains('input[name="room_Form[id_AS]"][value="7"]');
        $this->assertSelectorTextContains('input[name="room_Form[floor]"][value="5"]');
        $this->assertSelectorTextContains('input[name="room_Form[building]"][value="3"]');

        // Remplissage du formulaire pour modifier la salle
        $client->submitForm('Sauvegarder les modifications', [
            'room_form[name]' => 'Test',
            'room_form[id_AS]' => '8',  // Ensure this is a valid AS ID in your test database
            'room_form[floor]' => '6',  // Ensure this is a valid floor ID
            'room_form[building]' => '3', // Ensure this is a valid building ID
        ]);

        $this->assertResponseRedirects('/rooms');
        $client->followRedirect();

        // Vérification que la salle a bien été mise à jour
        $this->assertSelectorExists('td'); // Ensure the table has at least one row
        $this->assertSelectorTextContains('td:nth-child(2)', 'Test');
        $this->assertSelectorTextContains('td:nth-child(3)', '1');
        $this->assertSelectorTextContains('td:nth-child(4)', 'Informatique');
    }

    /*public function testDeleteRoom()
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