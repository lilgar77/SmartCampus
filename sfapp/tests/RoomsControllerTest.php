<?php

namespace App\Tests;

use App\Entity\Building;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Floor;
use App\Entity\AcquisitionSystem;
use App\Entity\Room;
use App\Repository\UserRepository;

class RoomsControllerTest extends WebTestCase
{

    public function testsetUpData()
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);
        $crawler = $client->request('GET', '/building/add');
        $form = $crawler->selectButton('Ajouter un batiment')->form([
            'building[NameBuilding]' => 'info',
            'building[AdressBuilding]' => 'Paris',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/building');
        $client->followRedirect();

        // Load the form page for adding a new system
        $crawler = $client->request('GET', '/acquisitionsyteme/add');

        // Fill in the form fields with test data
        $formas = $crawler->selectButton('Ajouter un Système d\'Acquisition')->form(
            [
                'acquisition_systeme[temperature]' => 244,
                'acquisition_systeme[CO2]' => 40,
                'acquisition_systeme[humidity]' => 5,
                'acquisition_systeme[name]' => 'TestSA-002',
                'acquisition_systeme[wording]' => 'Salle de réunion 2',
                'acquisition_systeme[macAdress]' => '00:00:00:00:00:33',
                'acquisition_systeme[etat]' => 1,
            ]
        );

        // Submit the form
        $client->submit($formas);

        $this->assertResponseRedirects('/acquisitionsysteme');
        $client->followRedirect();



    }
    public function testIndexPage()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);

        $crawler = $client->request('GET', '/rooms');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des Salles');
    }

    public function testAddRoom()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);

        $crawler = $client->request('GET', '/rooms/add');

        $this->assertResponseIsSuccessful();

        $identifier = $client->getContainer()->get('doctrine')->getRepository(Building::class)->findBuildingByName('info')->getId();
        $identifier2 = $client->getContainer()->get('doctrine')->getRepository(Floor::class)->findFloorByNumber(2)->getId();
        $identifier3 = $client->getContainer()->get('doctrine')->getRepository(AcquisitionSystem::class)->findASByName('TestSA-002')->getId();


        $form = $crawler->selectButton('Ajouter une Salle')->form([
            'room_form[name]' => 'Test Room',
            'room_form[id_AS]' => $identifier3,
            'room_form[floor]' => $identifier2,
            'room_form[building]' => $identifier,
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/rooms');
        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert', 'La salle "Test Room" a été ajoutée avec succès.');


    }


    public function testEditRoom()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);

        $identifier = $client->getContainer()->get('doctrine')->getRepository(Room::class)->findRoomByName('Test Room')->getId();
        $crawler = $client->request('GET', '/rooms/'.$identifier.'/edit');

        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sauvegarder les modifications')->form([
            'room_form[name]' => 'Test Room Updated',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/rooms');
        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert', 'La salle "Test Room Updated" a été modifiée avec succès.');
    }


    public function testDeleteRoom()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);

        $identifier = $client->getContainer()->get('doctrine')->getRepository(Room::class)->findRoomByName('Test Room Updated')->getId();
        $crawler = $client->request('POST', '/rooms/'.$identifier);

        $this->assertResponseRedirects('/rooms');
        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert', 'La salle "Test Room Updated" a été supprimée avec succès.');


        // delete full data

        $identifier = $client->getContainer()->get('doctrine')->getRepository(Building::class)->findBuildingByName('info')->getId();
        $identifier3 = $client->getContainer()->get('doctrine')->getRepository(AcquisitionSystem::class)->findASByName('TestSA-002')->getId();

        $client->request('POST', '/acquisitionsyteme/'.$identifier3);
        $client->request('POST', '/building/'.$identifier);

    }


}
