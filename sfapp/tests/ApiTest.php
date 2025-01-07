<?php

namespace App\Tests;

use App\Service\ApiService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiTest extends TestCase
{
    public function testGetCapturesByIntervalSuccess(): void
    {
        $HttpClient = $this->createMock(HttpClientInterface::class);
        $Response = $this->createMock(ResponseInterface::class);

        $Response->method('getStatusCode')->willReturn(200);

        $HttpClient->method('request')->willReturn($Response);

        $apiService = new ApiService($HttpClient);
        $result = $apiService->getCapturesByInterval('2025-01-01', '2025-01-06', 'temp', 1, 'sae34bdk1eq2');

        $this->assertIsArray($result);

    }

    public function testGetCapturesByIntervalFailure(): void
    {
        $HttpClient = $this->createMock(HttpClientInterface::class);
        $Response = $this->createMock(ResponseInterface::class);

        $Response->method('getStatusCode')->willReturn(500);

        $HttpClient->method('request')->willReturn($Response);

        $apiService = new ApiService($HttpClient);

        $this->expectException(\Exception::class);
        $apiService->getCapturesByInterval('2025-01-01', '2025-01-06', 'temp', 1, 'sae34bdk1eq2');
    }

    public function testGetLastCaptureSuccess(): void
    {
        $HttpClient = $this->createMock(HttpClientInterface::class);
        $Response = $this->createMock(ResponseInterface::class);

        $Response->method('getStatusCode')->willReturn(200);

        $HttpClient->method('request')->willReturn($Response);

        $apiService = new ApiService($HttpClient);
        $result = $apiService->getLastCapture('temp', 'sae34bdk1eq2');

        $this->assertIsArray($result);

    }

    public function testGetLastCaptureFailure(): void
    {
        $HttpClient = $this->createMock(HttpClientInterface::class);
        $Response = $this->createMock(ResponseInterface::class);

        $Response->method('getStatusCode')->willReturn(500);

        $HttpClient->method('request')->willReturn($Response);

        $apiService = new ApiService($HttpClient);

        $this->expectException(\Exception::class);
        $apiService->getLastCapture('temp', 'sae34bdk1eq2');
    }
}
