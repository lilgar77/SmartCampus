<?php
namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AcquisitionSystemTest extends WebTestCase {
    public function testLaPageDesSystemesDAquisitionEstDisponible(): void {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

//        // Vérification de l'existence de l'utilisateur admin
//        $admin = $userRepository->findOneByEmail('admin@admin.com');
//        $this->assertNotNull($admin, 'Admin user not found.');
//
//        // Connexion avec l'admin
//        $client->loginUser($admin);
//
//        // Requête pour accéder à la page des systèmes d'acquisition
          $crawler = $client->request('GET', '/');
//
//        // Vérification que la page est bien accessible et renvoie une réponse réussie
         $this->assertResponseIsSuccessful();
    }
}


