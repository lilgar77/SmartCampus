<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminTest extends WebTestCase
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
    public function testAjoutDeSalleInterditAuxTechniciens(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $technicien = $userRepository->findOneByEmail('technicien@technicien.com');

        $client->loginUser($technicien);
        $client->request('GET', '/rooms/add');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
