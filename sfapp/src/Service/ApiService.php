<?php
namespace App\Service;

use http\Params;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ApiService
{
    private HttpClientInterface $client;

    // Default username and password for the API
    private string $username;
    private string $userpass;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $parameterBag)
    {
        $this->client = $client;

        // Set the username and password for the API
        $this->username = $this->getStringParam($parameterBag, 'API_USERNAME');
        $this->userpass = $this->getStringParam($parameterBag, 'API_USERPASS');
    }

    /**
     * Cette fonction garantit que le paramètre est une chaîne de caractères.
     *
     * @param ParameterBagInterface $parameterBag
     * @param string $paramName
     * @return string
     */
    private function getStringParam(ParameterBagInterface $parameterBag, string $paramName): string
    {
        $value = $parameterBag->get($paramName);

        // Si la valeur n'est pas une chaîne, retourne une chaîne vide ou une valeur par défaut
        if (!is_string($value)) {
            return '';
        }

        return $value;
    }

    /**
     * @return array<int, array<string, mixed>> Le tableau des captures avec les données sous forme de tableau associatif.
     */
    public function getCapturesByInterval(string $date1, string $date2, string $name, int $page, string $dbname): array
    {
        $url = 'https://sae34.k8s.iut-larochelle.fr/api/captures/interval';

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

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => $headers,
                'query'   => $query,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Erreur HTTP : ' . $response->getStatusCode());
            }

            $responseData = $response->toArray();

            // Transform response to ensure it matches array<int, array<string, mixed>>
            return $this->validateResponse($responseData);
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la requête : ' . $e->getMessage());
        }
    }

    /**
     * @return array<int, array<string, mixed>> Le tableau des captures avec les données sous forme de tableau associatif.
     */
    public function getLastCapture(string $name, string $dbname): array
    {
        $url = 'https://sae34.k8s.iut-larochelle.fr/api/captures/last';

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

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => $headers,
                'query'   => $query,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Erreur HTTP : ' . $response->getStatusCode());
            }

            $responseData = $response->toArray();

            // Transform response to ensure it matches array<int, array<string, mixed>>
            return $this->validateResponse($responseData);
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la requête : ' . $e->getMessage());
        }
    }

    /**
     * Validates and transforms the API response to ensure it matches the expected type.
     *
     * @param array<mixed> $response
     * @return array<int, array<string, mixed>>
     */
    private function validateResponse(array $response): array
    {
        $validated = [];
        foreach ($response as $item) {
            if (is_array($item) && $this->isAssociativeArray($item)) {
                /** @var array<string, mixed> $item */
                $validated[] = $item;
            }
        }
        return $validated;
    }

    /**
     * Helper function to check if an array is associative with string keys.
     *
     * @param array<mixed> $array
     * @return bool
     */
    private function isAssociativeArray(array $array): bool
    {
        foreach (array_keys($array) as $key) {
            if (!is_string($key)) {
                return false;
            }
        }
        return true;
    }

    public function updateLastCapturesForRooms(RoomRepository $roomRepository, EntityManagerInterface $entityManager): void
    {
        $rooms = $roomRepository->findRoomWithAsInstalled();

        foreach ($rooms as $room) {
            $name = $room->getName();
            if ($name === null) {
                continue;
            }

            $roomDbInfo = $roomRepository->getRoomDb($name);
            $dbname = $roomDbInfo['dbname'] ?? null;

            if ($dbname === null) {
                continue;
            }

            try {
                $lastTemperature = $this->getLastCapture('temp', $dbname)[0]['valeur'] ?? null;
                $lastHumidity = $this->getLastCapture('hum', $dbname)[0]['valeur'] ?? null;
                $lastCO2 = $this->getLastCapture('co2', $dbname)[0]['valeur'] ?? null;

                $lastTemperature = is_numeric($lastTemperature) ? (int) $lastTemperature : 0;
                $lastHumidity = is_numeric($lastHumidity) ? (int) $lastHumidity : 0;
                $lastCO2 = is_numeric($lastCO2) ? (int) $lastCO2 : 0;

                $acquisitionSystem = $room->getIdAS();

                if ($acquisitionSystem !== null) {
                    $acquisitionSystem->setTemperature($lastTemperature);
                    $acquisitionSystem->setHumidity($lastHumidity);
                    $acquisitionSystem->setCO2($lastCO2);
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        $entityManager->flush();
    }
}
