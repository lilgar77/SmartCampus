<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\AcquisitionSystem;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\Tools\SchemaTool;


class AcquisitionSystemTest extends WebTestCase
{
    private ?int $id_AS; // Variable to store the AcquisitionSystem ID for later use

    // Test case for accessing the Acquisition System list page
    private UserPasswordHasherInterface $passwordHasher;
    private $client; // Stocker le client ici pour l'utiliser dans les tests
    private $entityManager;

    public function setUp(): void
    {
        parent::setUp();
        // Initialiser le client
        $this->client = static::createClient();

        // Récupérer l'EntityManager via le conteneur du client
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        // Réinitialiser la base de données (vider toutes les tables)
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        if (!empty($metadata)) {
            // Supprimer les schémas existants
            $schemaTool->dropSchema($metadata);

            // Recréer les schémas
            $schemaTool->createSchema($metadata);
        }

        // Créer un utilisateur admin pour les tests
        $user = new User();
        $user->setEmail('admin@admin.com');
        $user->setUsername('admin');
        $hashedPassword = $this->client->getContainer()
            ->get(UserPasswordHasherInterface::class)
            ->hashPassword($user, 'admin');
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_ADMIN']);

        $acquisitionSystem1 = new AcquisitionSystem();
        $acquisitionSystem1->setName('TestSA-001')
            ->setWording('Salle de réunion')
            ->setMacAdress('00:00:00:00:00:01')
            ->setEtat(EtatAS::Installer);
        $this->entityManager->persist($acquisitionSystem1);

        // Persister et sauvegarder l'utilisateur
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function testLaPageDesSystemesDAquisitionEstDisponible(): void
    {
        // Récupérer l'utilisateur admin et se connecter
        $admin = $this->entityManager->getRepository(User::class)->findOneByEmail('admin@admin.com');
        $this->assertNotNull($admin, 'Admin user not found.');
        $this->client->loginUser($admin);

        $crawler = $this->client->request('GET', '/acquisitionsysteme');

        // Vérifier que la réponse est réussie
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des Systèmes d\'acquisition');
    }


/*
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
    }*/

    // Test case for adding a new Acquisition System
    public function testAddAS(): void
    {
        $admin = $this->entityManager->getRepository(User::class)->findOneByEmail('admin@admin.com');
        $this->assertNotNull($admin, 'Admin user not found.');
        $this->client->loginUser($admin);


        // Load the form page for adding a new system
        $crawler = $this->client->request('GET', '/acquisitionsyteme/add');
        $this->assertResponseIsSuccessful();

        // Fill in the form fields with test data
       $form = $crawler->selectButton('Ajouter un Système d\'Acquisition')->form(
           [
               'acquisition_systeme[name]' => 'TestSA-001',
               'acquisition_systeme[wording]' => 'Salle de réunion',
               'acquisition_systeme[macAdress]' => '00:00:00:00:00:05',
               'acquisition_systeme[etat]' => 2,
           ]
        );

        // Submit the form
        $this->client->submit($form);


        // Verify redirection to the list page after submission
        $this->assertResponseRedirects('/acquisitionsysteme');  // Vérifie la redirection vers la page d'acquisition

        $response = $this->client->getResponse();
        $this->assertResponseRedirects('/acquisitionsysteme');

        // Check if the success message appears
        //$this->assertSelectorTextContains('div.alert', 'Système d\'acquisition "TestSA-001" ajouté avec succès');
    }

    // Test case for editing an existing Acquisition System
    public function testEditAS(): void
    {
        $admin = $this->entityManager->getRepository(User::class)->findOneByEmail('admin@admin.com');
        $this->assertNotNull($admin, 'Admin user not found.');
        $this->client->loginUser($admin);


        // Retrieve the ID of the AcquisitionSystem based on its name for editing
        $this->id_AS = $this->client->getContainer()->get('doctrine')->getRepository(AcquisitionSystem::class)->findASByName('TestSA-001')->getId();

        // Load the edit form for the selected AcquisitionSystem
        $crawler = $this->client->request('GET', '/acquisitionsyteme/'. $this->id_AS .'/edit');

//        //Modif #########################
//        $this->assertResponseIsSuccessful();
//        //Modif #########################
//        $this->assertGreaterThan(0, $crawler->filter('form')->count(), 'Form not found on the page.');
//        // Fill in the form with updated data
        $form = $crawler->selectButton('Sauvegarder les modifications')->form(
            [
                'acquisition_systeme[name]' => 'TestSA-001',
                'acquisition_systeme[wording]' => 'Salle de réunion',
                'acquisition_systeme[macAdress]' => '00:00:00:00:00:05',
                'acquisition_systeme[etat]' => 2,
            ]
        );

        // Submit the form
        $this->client->submit($form);

        // Verify redirection after form submission
        $this->assertResponseRedirects('/acquisitionsysteme');

        // Check for success message after modification
        //$this->assertSelectorTextContains('div.alert', 'Système d\'acquisition "TestSA-Updated" modifié avec succès');
    }

    // Test case for deleting an existing Acquisition System
    public function testDeleteAS(): void
    {
        $admin = $this->entityManager->getRepository(User::class)->findOneByEmail('admin@admin.com');
        $this->assertNotNull($admin, 'Admin user not found.');
        $this->client->loginUser($admin);


        // Retrieve the ID of the AcquisitionSystem that is to be deleted
        $this->id_AS = $this->client->getContainer()->get('doctrine')->getRepository(AcquisitionSystem::class)->findASByName('TestSA-Updated')->getId();

        // Send a POST request to delete the system
        $crawler = $this->client->request('POST', '/acquisitionsyteme/'. $this->id_AS);

        // Verify redirection after the deletion
        $this->assertResponseRedirects('/acquisitionsysteme');

        // Check for success message after deletion
        //$this->assertSelectorTextContains('div.alert', 'Système d\'acquisition "TestSA-Updated" supprimé avec succès');*/
    }

    public function tearDown(): void
    {
        parent::tearDown();
        // Réinitialiser l'EntityManager après chaque test
        $this->entityManager->close();
        $this->entityManager = null;
    }
}