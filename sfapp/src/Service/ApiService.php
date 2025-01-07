<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;

class ApiService
{
    private HttpClientInterface $client;



    // Default username and password for the API
    private string $username = 'l1eq1';
    private string $userpass = 'dicvex-Zofsip-4juqru';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }


    public function getCapturesByInterval(string $date1, string $date2, string $name, int $page, string $dbname): array
    {
        // URL of the API for the captures in interval
        $url = 'https://sae34.k8s.iut-larochelle.fr/api/captures/interval';

        // Headers and query for the API request
        $headers = [
            'accept'   => 'application/ld+json',
            'dbname'   => $dbname,
            'username' => $this->username,
            'userpass' => $this->userpass,
        ];

        $query = [
            'date1' => $date1,
            'date2' => $date2,
            'nom' => $name,
            'page'  => $page,
        ];


        // Try to make the request to the API
        try {
            $response = $this->client->request('GET', $url, [
                'headers' => $headers,
                'query'   => $query,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Erreur HTTP : ' . $response->getStatusCode());
            }

            return $response->toArray();
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la requête : ' . $e->getMessage());
        }
    }

    public function getLastCapture(string $name, string $dbname): array
    {

        // URL of the API for the last capture
        $url = 'https://sae34.k8s.iut-larochelle.fr/api/captures/last';

         // Headers and query for the API request
        $headers = [
            'accept'   => 'application/ld+json',
            'dbname'   => $dbname,
            'username' => $this->username,
            'userpass' => $this->userpass,
        ];

        $query = [
            'nom' => $name,
            'limit' => 1,
            'page'  => 1,
        ];

        // Try to make the request to the API
        try {
            $response = $this->client->request('GET', $url, [
                'headers' => $headers,
                'query'   => $query,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Erreur HTTP : ' . $response->getStatusCode());
            }

            return $response->toArray();
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la requête : ' . $e->getMessage());
        }
    }

    public function updateLastCapturesForRooms(RoomRepository $roomRepository, EntityManagerInterface $entityManager): void
    {
        // Récupérer toutes les salles
        $rooms = $roomRepository->findRoomWithAsInstalled();

        foreach ($rooms as $room) {
            // Récupérer la base de données associée à la salle
            $roomDbInfo = $roomRepository->getRoomDb($room->getName());
            $dbname = $roomDbInfo['dbname'] ?? null;

            if (!$dbname) {
                continue; // Passer à la salle suivante si la base de données n'est pas définie
            }

            try {
                // Récupérer les dernières valeurs depuis l'API
                $lastTemperature = $this->getLastCapture('temp', $dbname)[0]['valeur'] ?? null;
                $lastHumidity = $this->getLastCapture('hum', $dbname)[0]['valeur'] ?? null;
                $lastCO2 = $this->getLastCapture('co2', $dbname)[0]['valeur'] ?? null;

                // Mettre à jour le système d'acquisition avec les nouvelles données
                if ($lastTemperature !== null) {
                    $room->getIdAS()->setTemperature($lastTemperature);
                }

                if ($lastHumidity !== null) {
                    $room->getIdAS()->setHumidity($lastHumidity);
                }

                if ($lastCO2 !== null) {
                    $room->getIdAS()->setCO2($lastCO2);
                }


            } catch (\Exception $e) {
                // En cas d'erreur API, continuer avec la salle suivante
                continue;
            }
        }

        // Sauvegarder toutes les modifications dans la base de données
        $entityManager->flush();
    }
}