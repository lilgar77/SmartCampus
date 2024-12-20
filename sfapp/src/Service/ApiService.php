<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiService
{
    private HttpClientInterface $client;

    private $dbname = 'sae34bdl1eq1';
    private $username = 'l1eq1';
    private $userpass = 'dicvex-Zofsip-4juqru';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getCapturesByInterval(string $date1, string $date2, string $name, int $page): array
    {
        $url = 'https://sae34.k8s.iut-larochelle.fr/api/captures/interval';

        $headers = [
            'accept'   => 'application/ld+json',
            'dbname'   => $this->dbname,
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

            return $response->toArray();
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la requête : ' . $e->getMessage());
        }
    }

    public function getLastCapture(string $name): array
    {
        $url = 'https://sae34.k8s.iut-larochelle.fr/api/captures/last';

        $headers = [
            'accept'   => 'application/ld+json',
            'dbname'   => $this->dbname,
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

            return $response->toArray();
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la requête : ' . $e->getMessage());
        }
    }
}