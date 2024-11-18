<?php

namespace App\Tests;

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

        // Verify the page contains the correct heade
        $this->assertSelectorTextContains('h1', 'Liste des Etages');
    }

    // Test case for adding a new Floor
    public function testAddRoom()
    {
        $client = static::createClient();
        // Load the form page for adding a new system
        $crawler = $client->request('GET', '/floor/add');

        // Check if the response was successful (status 200)
        $this->assertResponseIsSuccessful();

        // Fill in the form fields with test data
        $form = $crawler->selectButton('Ajouter un Etage')->form([
            'floor[numberFloor]' => '5',
            'floor[IdBuilding]' => '1', // Batiment
        ]);

        // Submit the form
        $client->submit($form);

        // Verify redirection to the list page after submission
        $this->assertResponseRedirects('/floor');
        $client->followRedirect();

        // Check if the floor is added
        $this->assertSelectorTextContains('tr:nth-child(5) td:nth-child(2)', '5');
        $this->assertSelectorTextContains('tr:nth-child(5) td:nth-child(3)', 'Informatique');
    }

    //Test case for editing an existing Floor
    public function testEditRoom()
    {
        $client = static::createClient();

        // Retrieve the ID of the Floor based on its id for editing
        $this->id_Floor = $client->getContainer()->get('doctrine')->getRepository(Floor::class)->findFloorByNumber(1)->getId();

        // Load the edit form for the selected Floor
        $crawler = $client->request('GET', '/floor/'. $this->id_Floor.'/edit');

        // Check if the response was successful (status 200)
        $this->assertResponseIsSuccessful();

        // Fill in the form with updated data
        $form = $crawler->selectButton('Sauvegarder les modifications')->form([
            'floor[numberFloor]' => '7',
            'floor[IdBuilding]' => '1',
        ]);

        // Submit the form
        $client->submit($form);

        // Verify redirection after form submission
        $this->assertResponseRedirects('/floor');
        $client->followRedirect();

        // Check for success message after modification
        $this->assertSelectorTextContains('div.alert', 'L\'étage "7" modifié avec succès');
    }

    // Test case for deleting an existing Floor
    public function testDeleteRoom()
    {
        $client = static::createClient();

        // Retrieve the ID of the Floor based on its id for editing
        $this->id_Floor = $client->getContainer()->get('doctrine')->getRepository(Floor::class)->findFloorByNumber(1)->getId();

        // Load the edit form for the selected Floor
        $crawler = $client->request('GET', '/floor/'. $this->id_Floor);

        // Check if the response was successful (status 200)
        $this->assertResponseIsSuccessful();

        // Verify redirection after the deletion
        $this->assertResponseRedirects('/floor');
        $client->followRedirect();

        // Check for success message after deletion
        $this->assertSelectorTextContains('div.alert', 'L\'étage "7" supprimé avec succès');
    }

}
