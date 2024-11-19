<?php

namespace App\Tests;

use App\Entity\AcquisitionSystem;
use App\Entity\Building;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BuildingTest extends WebTestCase
{
    private ?int $id_Building;
    public function testBuildingPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/building');

        $this->assertResponseIsSuccessful();
    }

    // Test to add a new Building
    public function testAddBuilding(): void {
        $client = static::createClient();
        $crawler = $client->request('GET', '/building/add');

        $this->assertResponseIsSuccessful();

        $formBuilding = $crawler->selectButton('Ajouter un batiment')->form(
            [
            'building[NameBuilding]' => 'Droit',
            'building[AdressBuilding]' => 'LaRochelle',
            ]
        );
        $client->submit($formBuilding);

        $this->assertResponseRedirects('/building');
        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert', 'Bâtiment "Droit" ajouté avec succès');
    }

    public function testEditBuilding(): void {
        $client = static::createClient();
        $this->id_Building = $client->getContainer()->get('doctrine')->getRepository(Building::class)->findBuildingByName('Droit')->getId();

        $crawler = $client->request('GET', '/building/'.$this->id_Building.'/edit');

        $formBuilding = $crawler->selectButton('Sauvegarder les modifications')->form(
            [
                'building[NameBuilding]' => 'Droit',
                'building[AdressBuilding]' => 'Marseille',
            ]
        );
        $client->submit($formBuilding);

        $this->assertResponseRedirects('/building');
        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert', 'Bâtiment "Droit" modifié avec succès');
    }
    public function testDeleteBuilding(): void
    {
        $client = static::createClient();

        // Retrieve the ID of the AcquisitionSystem that is to be deleted
        $this->id_Building = $client->getContainer()->get('doctrine')->getRepository(Building::class)->findBuildingByName('Droit')->getId();

        // Send a POST request to delete the system
        $crawler = $client->request('POST', '/building/'. $this->id_Building);

        // Verify redirection after the deletion
        $this->assertResponseRedirects('/building');
        $client->followRedirect();

        // Check for success message after deletion
        $this->assertSelectorTextContains('div.alert', 'Bâtiment "Droit" supprimé avec succès');


    }
}
