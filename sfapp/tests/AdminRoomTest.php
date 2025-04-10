<?php

namespace App\Tests;

use App\Entity\Room;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminRoomTest extends WebTestCase
{
    public function testAjoutDeSalleNecessiteAuthentification(): void
    {
        $client = static::createClient();
        $client->request('GET', '/rooms/add');
        $this->assertResponseRedirects('/login');
    }

    public function testAjoutDeSalleAccessibleAuxAdmins(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);
        $client->request('GET', '/rooms/add');
        $this->assertResponseIsSuccessful();
    }

    public function testListeDeSalleAccessibleAuxAdmins(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);
        $client->request('GET', '/rooms');
        $this->assertResponseIsSuccessful();
    }

    public function testModifDeSalleAccessibleAuxAdmins(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);
        $identifier = $client->getContainer()->get('doctrine')->getRepository(Room::class)->findRoomByName('D304')->getId();
        $client->request('GET', '/rooms/' . $identifier . '/edit');
        $this->assertResponseIsSuccessful();
    }
}