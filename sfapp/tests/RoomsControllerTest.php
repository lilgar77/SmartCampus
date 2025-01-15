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
    public function testIndexPage()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

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

        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);

        $crawler = $client->request('GET', '/rooms/add');

        $this->assertResponseIsSuccessful();

        $identifier = $client->getContainer()->get('doctrine')->getRepository(Building::class)->findOneBy(['NameBuilding' => 'Informatique'])->getId();
        $identifier2 = $client->getContainer()->get('doctrine')->getRepository(Floor::class)->findFloorByNumber(2)->getId();

        $form = $crawler->selectButton('Ajouter une Salle')->form([
            'room_form[name]' => 'Test Room',
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

        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);

        $identifier = $client->getContainer()->get('doctrine')->getRepository(Room::class)->findRoomByName('Test Room Updated')->getId();
        $crawler = $client->request('POST', '/rooms/'.$identifier);

        $this->assertResponseRedirects('/rooms');
        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert', 'La salle "Test Room Updated" a été supprimée avec succès.');
    }

}
