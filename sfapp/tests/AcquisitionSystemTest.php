<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\AcquisitionSystem;

class AcquisitionSystemTest extends WebTestCase
{
    private ?int $id_AS; // Variable to store the AcquisitionSystem ID for later use

    // Test case for accessing the Acquisition System list page
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/acquisitionsysteme');

        // Check if the response was successful (status 200)
        $this->assertResponseIsSuccessful();

        // Verify the page contains the correct header
        $this->assertSelectorTextContains('h1', 'Liste des Systèmes d\'acquisition');
    }

    // Test case for adding a new Acquisition System
    public function testAddAS(): void
    {
        $client = static::createClient();

        // Load the form page for adding a new system
        $crawler = $client->request('GET', '/acquisitionsyteme/add');
        $this->assertResponseIsSuccessful();

        // Fill in the form fields with test data
        $form = $crawler->selectButton('Ajouter un Système d\'Acquisition')->form(
            [
                'acquisition_systeme[temperature]' => 20,
                'acquisition_systeme[CO2]' => 400,
                'acquisition_systeme[humidity]' => 50,
                'acquisition_systeme[name]' => 'TestSA-001',
                'acquisition_systeme[wording]' => 'Salle de réunion',
                'acquisition_systeme[macAdress]' => '00:00:00:00:00:05',
                'acquisition_systeme[etat]' => 1,
            ]
        );

        // Submit the form
        $client->submit($form);

        // Verify redirection to the list page after submission
        $this->assertResponseRedirects('/acquisitionsysteme');
        $client->followRedirect();

        // Check if the success message appears
        $this->assertSelectorTextContains('div.alert', 'Système d\'acquisition "TestSA-001" ajouté avec succès');
    }

    // Test case for editing an existing Acquisition System
    public function testEditAS(): void
    {
        $client = static::createClient();

        $acquisitionSystem = $client->getContainer()->get('doctrine')
            ->getRepository(AcquisitionSystem::class)
            ->findASByName('TestSA-001');

        $this->assertNotNull($acquisitionSystem, 'Acquisition System not found.');
        $this->id_AS = $acquisitionSystem->getId();

        // Load the edit form for the selected AcquisitionSystem
        $crawler = $client->request('GET', '/acquisitionsyteme/'. $this->id_AS .'/edit');

        // Fill in the form with updated data
        $form = $crawler->selectButton('Sauvegarder les modifications')->form(
            [
                'acquisition_systeme[temperature]' => 25,
                'acquisition_systeme[CO2]' => 450,
                'acquisition_systeme[humidity]' => 55,
                'acquisition_systeme[name]' => 'TestSA-Updated',
                'acquisition_systeme[wording]' => 'Salle updated',
                'acquisition_systeme[macAdress]' => '00:00:00:00:00:04',
                'acquisition_systeme[etat]' => 2,
            ]
        );

        // Submit the form
        $client->submit($form);

        // Verify redirection after form submission
        $this->assertResponseRedirects('/acquisitionsysteme');
        $client->followRedirect();

        // Check for success message after modification
        $this->assertSelectorTextContains('div.alert', 'Système d\'acquisition "TestSA-Updated" modifié avec succès');
    }

    // Test case for deleting an existing Acquisition System
    public function testDeleteAS(): void
    {
        $client = static::createClient();

        // Retrieve the ID of the AcquisitionSystem that is to be deleted
        $acquisitionSystem = $client->getContainer()->get('doctrine')
            ->getRepository(AcquisitionSystem::class)
            ->findASByName('TestSA-Updated');

        $this->assertNotNull($acquisitionSystem, 'Acquisition System not found.');
        $this->id_AS = $acquisitionSystem->getId();

        // Send a POST request to delete the system
        $crawler = $client->request('POST', '/acquisitionsyteme/'. $this->id_AS);

        // Verify redirection after the deletion
        $this->assertResponseRedirects('/acquisitionsysteme');
        $client->followRedirect();

        // Check for success message after deletion
        $this->assertSelectorTextContains('div.alert', 'Système d\'acquisition "TestSA-Updated" supprimé avec succès');
    }
}