<?php

namespace App\Tests;

use App\Entity\Building;
use App\Entity\Floor;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FloorTest extends WebTestCase
{
    private ?int $idFloor; // Variable to store the Floor ID for later use

    // Test case for accessing the Floor list page
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/floor');

        // Check if the response was successful (status 200)
        $this->assertResponseIsSuccessful();

        // Verify the page contains the correct head
        $this->assertSelectorTextContains('h1', 'Liste des Etages');
    }

    // Test case for adding a new Floor
    public function testAddFloor()
    {
        $client = static::createClient();

        // Load the edit form for the selected Building
        $crawler = $client->request('GET', '/building/add');

        // Fill in the form with updated data
        $form = $crawler->selectButton('Ajouter un batiment')->form([
            'building[NameBuilding]' => 'Info',
            'building[AdressBuilding]' => 'Rue',
        ]);

        // Submit the form
        $client->submit($form);
        // Verify redirection after form submission
        $this->assertResponseRedirects('/building');
        $client->followRedirect();

        // Load the form page for adding a new system
        $id_Building = $client->getContainer()->get('doctrine')->getRepository(Building::class)->findBuildingByName('Info')->getId();
        $crawler = $client->request('GET', '/floor/add');

        // Check if the response was successful (status 200)
        $this->assertResponseIsSuccessful();

        // Fill in the form fields with test data
        $form = $crawler->selectButton('Ajouter un Etage')->form([
            'floor[numberFloor]' => '1',
            'floor[IdBuilding]' => $id_Building,
        ]);

        // Submit the form
        $client->submit($form);

        // Verify redirection to the list page after submission
        $this->assertResponseRedirects('/floor');
        $client->followRedirect();

        // Check if the floor is added
        //$this->assertSelectorTextContains('div.alert', 'Étage "1" ajouté avec succès');
    }

    //Test case for editing an existing Floor
    public function testEditFLoor()
    {
        $client = static::createClient();

        // Load the edit form for the selected Building
        $crawler = $client->request('GET', '/building/add');

        // Fill in the form with updated data
        $form = $crawler->selectButton('Ajouter un batiment')->form([
            'building[NameBuilding]' => 'Info',
            'building[AdressBuilding]' => 'Rue',
        ]);

        // Submit the form
        $client->submit($form);
        // Verify redirection after form submission
        $this->assertResponseRedirects('/building');
        $client->followRedirect();

        // Load the form page for adding a new system
        $id_Building = $client->getContainer()->get('doctrine')->getRepository(Building::class)->findBuildingByName('Info')->getId();
        $crawler = $client->request('GET', '/floor/add');

        // Check if the response was successful (status 200)
        $this->assertResponseIsSuccessful();

        // Fill in the form fields with test data
        $form = $crawler->selectButton('Ajouter un Etage')->form([
            'floor[numberFloor]' => '1',
            'floor[IdBuilding]' => $id_Building,
        ]);

        // Submit the form
        $client->submit($form);

        // Verify redirection to the list page after submission
        $this->assertResponseRedirects('/floor');
        $client->followRedirect();

        // Retrieve the ID of the Floor based on its id for editing
        $this->id_Floor = $client->getContainer()->get('doctrine')->getRepository(Floor::class)->findFloorByNumber(1)->getId();

        // Load the edit form for the selected Floor
        $crawler = $client->request('GET', '/floor/'. $this->id_Floor.'/edit');

        // Check if the response was successful (status 200)
        $this->assertResponseIsSuccessful();

        $id_Building = $client->getContainer()->get('doctrine')->getRepository(Building::class)->findBuildingByName('Info')->getId();

        // Fill in the form with updated data
        $form = $crawler->selectButton('Sauvegarder les modifications')->form([
            'floor[numberFloor]' => '8',
            'floor[IdBuilding]' =>  $id_Building,
        ]);

        // Submit the form
        $client->submit($form);

        // Verify redirection after form submission
        $this->assertResponseRedirects('/floor');
        $client->followRedirect();

        // Check for success message after modification
        //$this->assertSelectorTextContains('div.alert', 'Étage "8" modifié avec succès');
    }

    // Test case for deleting an existing Floor
    public function testDeleteFloor()
    {
        $client = static::createClient();

        // Load the edit form for the selected Building
        $crawler = $client->request('GET', '/building/add');

        // Fill in the form with updated data
        $form = $crawler->selectButton('Ajouter un batiment')->form([
            'building[NameBuilding]' => 'Info',
            'building[AdressBuilding]' => 'Rue',
        ]);

        // Submit the form
        $client->submit($form);
        // Verify redirection after form submission
        $this->assertResponseRedirects('/building');
        $client->followRedirect();

        // Load the form page for adding a new system
        $id_Building = $client->getContainer()->get('doctrine')->getRepository(Building::class)->findBuildingByName('Info')->getId();
        $crawler = $client->request('GET', '/floor/add');

        // Check if the response was successful (status 200)
        $this->assertResponseIsSuccessful();

        // Fill in the form fields with test data
        $form = $crawler->selectButton('Ajouter un Etage')->form([
            'floor[numberFloor]' => '1',
            'floor[IdBuilding]' => $id_Building,
        ]);

        // Submit the form
        $client->submit($form);

        // Verify redirection to the list page after submission
        $this->assertResponseRedirects('/floor');
        $client->followRedirect();

        // Retrieve the ID of the Floor based on its id for editing
        $this->id_Floor = $client->getContainer()->get('doctrine')->getRepository(Floor::class)->findFloorByNumber(1)->getId();

        // Load the edit form for the selected Floor
        $crawler = $client->request('GET', '/floor');

        // Send a POST request to delete the system
        $crawler = $client->request('POST', '/floor/'. $this->id_Floor);

        // Verify redirection after the deletion
        $this->assertResponseRedirects('/floor');
        $client->followRedirect();

        // Check for success message after deletion
        //$this->assertSelectorTextContains('div.alert', 'Étage "7" supprimé avec succès');
    }

}
