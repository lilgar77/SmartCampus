<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Building;
use App\Entity\Floor;
use App\Repository\UserRepository;

class FloorTest extends WebTestCase
{
    private ?int $idFloor;

    // Test case for accessing the Floor list page
    public function testFloorPage(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Ensure the admin user exists
        $admin = $userRepository->findOneByEmail('admin@admin.com');
        $this->assertNotNull($admin, 'Admin user not found.');

        // Log in as the admin
        $client->loginUser($admin);

        // Request the page
        $crawler = $client->request('GET', '/floor');

        // Check if the response was successful
        $this->assertResponseIsSuccessful();

        // Verify the page contains the correct heading
        $this->assertSelectorTextContains('h1', 'Liste des Etages');
    }

    public function testAddFloor()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Ensure the admin user exists
        $admin = $userRepository->findOneByEmail('admin@admin.com');
        $this->assertNotNull($admin, 'Admin user not found.');

        // Log in as the admin
        $client->loginUser($admin);

        // Load the form page for adding a new system
        $id_Building = $client->getContainer()->get('doctrine')->getRepository(Building::class)->findOneBy(['NameBuilding' => 'Informatique'])->getId();
        $crawler = $client->request('GET', '/floor/add');

        // Check if the response was successful (status 200)
        $this->assertResponseIsSuccessful();

        // Fill in the form fields with test data
        $form = $crawler->selectButton('Ajouter un Etage')->form([
            'floor[numberFloor]' => '4',
            'floor[IdBuilding]' => $id_Building,
        ]);

        // Submit the form
        $client->submit($form);

        // Verify redirection to the list page after submission
        $this->assertResponseRedirects('/floor');
        $client->followRedirect();

        // Check if the floor is added
        $this->assertSelectorTextContains('div.alert', 'Étage "4" ajouté avec succès');
    }

    public function testEditFloor()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Ensure the admin user exists
        $admin = $userRepository->findOneByEmail('admin@admin.com');
        $this->assertNotNull($admin, 'Admin user not found.');

        // Log in as the admin
        $client->loginUser($admin);
        // Retrieve the ID of the Floor based on its id for editing
        $this->idFloor = $client->getContainer()->get('doctrine')->getRepository(Floor::class)->findFloorByNumber(4)->getId();

        // Load the edit form for the selected Floor
        $crawler = $client->request('GET', '/floor/'. $this->idFloor.'/edit');

        // Check if the response was successful (status 200)
        $this->assertResponseIsSuccessful();

        $id_Building = $client->getContainer()->get('doctrine')->getRepository(Building::class)->findOneBy(['NameBuilding' => 'Tech de co'])->getId();

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
        $this->assertSelectorTextContains('div.alert', 'Étage "8" modifié avec succès');

    }

    public function testDeleteFloor()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Ensure the admin user exists
        $admin = $userRepository->findOneByEmail('admin@admin.com');
        $this->assertNotNull($admin, 'Admin user not found.');

        // Log in as the admin
        $client->loginUser($admin);

        // Retrieve the ID of the Floor based on its id for editing
        $this->idFloor = $client->getContainer()->get('doctrine')->getRepository(Floor::class)->findFloorByNumber(8)->getId();

        // Send a POST request to delete the system
        $crawler = $client->request('POST', '/floor/'. $this->idFloor);

        // Verify redirection after the deletion
        $this->assertResponseRedirects('/floor');
        $client->followRedirect();

        // Check for success message after deletion
        $this->assertSelectorTextContains('div.alert', 'Étage "8" supprimé avec succès');
    }
}
