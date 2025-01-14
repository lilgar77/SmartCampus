<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\AcquisitionSystem;

class AcquisitionSystemTest extends WebTestCase
{
    private ?int $id_AS; // Variable to store the AcquisitionSystem ID for later use

    // Test case for accessing the Acquisition System list page
    public function testLaPageDesSystemesDAquisitionEstDisponible(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Ensure the admin user exists
        $admin = $userRepository->findOneByEmail('admin@admin.com');
        $this->assertNotNull($admin, 'Admin user not found.');

        // Log in as the admin
        $client->loginUser($admin);

        $crawler = $client->request('GET', '/acquisitionsysteme');

        // Check if the response was successful (status 200)
        $this->assertResponseIsSuccessful();

        // Verify the page contains the correct header
        $this->assertSelectorTextContains('h1', 'Liste des Systèmes d\'acquisition');
    }

    // Test case for adding a new Acquisition System
    public function testDAjoutDeSystemeDacquisionS(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Ensure the admin user exists
        $admin = $userRepository->findOneByEmail('admin@admin.com');
        $this->assertNotNull($admin, 'Admin user not found.');

        // Log in as the admin
        $client->loginUser($admin);


        // Load the form page for adding a new system
        $crawler = $client->request('GET', '/acquisitionsyteme/add');
        $this->assertResponseIsSuccessful();

        // Fill in the form fields with test data
        $form = $crawler->selectButton('Ajouter un Système d\'Acquisition')->form(
            [
                'acquisition_systeme[name]' => 'TestSA-001',
                'acquisition_systeme[wording]' => 'Salle de réunion',
                'acquisition_systeme[macAdress]' => '00:00:00:00:00:05',
                'acquisition_systeme[etat]' => 0,
            ]
        );

        // Submit the form
        $client->submit($form);

        // Verify redirection to the list page after submission
        $this->assertResponseRedirects('/acquisitionsysteme');  // Vérifie la redirection vers la page d'acquisition

        // Check if the success message appears
        //$this->assertSelectorTextContains('div.alert', 'Système d\'acquisition "TestSA-001" ajouté avec succès');
    }

    // Test case for editing an existing Acquisition System
    public function testDEditionDeSystemeDacquision(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Ensure the admin user exists
        $admin = $userRepository->findOneByEmail('admin@admin.com');
        $this->assertNotNull($admin, 'Admin user not found.');

        // Log in as the admin
        $client->loginUser($admin);


        // Retrieve the ID of the AcquisitionSystem based on its name for editing
        $this->id_AS = $client->getContainer()->get('doctrine')->getRepository(AcquisitionSystem::class)->findASByName('TestSA-001')->getId();

        // Load the edit form for the selected AcquisitionSystem
        $crawler = $client->request('GET', '/acquisitionsyteme/'. $this->id_AS .'/edit');

        // Fill in the form with updated data
        $form = $crawler->selectButton('Sauvegarder les modifications')->form(
            [
                'acquisition_systeme[name]' => 'TestSA-Updated',
                'acquisition_systeme[wording]' => 'Salle updated',
                'acquisition_systeme[macAdress]' => '00:00:00:00:00:08',
                'acquisition_systeme[etat]' => 2,
            ]
        );

        // Submit the form
        $client->submit($form);

        // Verify redirection after form submission
        $this->assertResponseRedirects('/acquisitionsysteme');

        // Check for success message after modification
        //$this->assertSelectorTextContains('div.alert', 'Système d\'acquisition "TestSA-Updated" modifié avec succès');
    }

    // Test case for deleting an existing Acquisition System
    public function testDeSuppressionDeSystemeDacquision(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Ensure the admin user exists
        $admin = $userRepository->findOneByEmail('admin@admin.com');
        $this->assertNotNull($admin, 'Admin user not found.');

        // Log in as the admin
        $client->loginUser($admin);


        // Retrieve the ID of the AcquisitionSystem that is to be deleted
        $this->id_AS = $client->getContainer()->get('doctrine')->getRepository(AcquisitionSystem::class)->findASByName('TestSA-Updated')->getId();

        // Send a POST request to delete the system
        $crawler = $client->request('POST', '/acquisitionsyteme/'. $this->id_AS);

        // Verify redirection after the deletion
        $this->assertResponseRedirects('/acquisitionsysteme');

        // Check for success message after deletion
    }
}