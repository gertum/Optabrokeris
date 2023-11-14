<?php

namespace App\Solver;

use GuzzleHttp\Client;

class SolverClientSchool implements SolverClient
{
    private const TIMETABLE_URI = '/api/time-table';

    private string $type;
    private string $url;

    public function __construct(string $type, string $url)
    {
        $this->type = $type;
        $this->url = $url;

    }

    public function registerData($data): int
    {
        $client = new Client();

        $url = sprintf('%s%s', $this->url, self::TIMETABLE_URI);
        $response = $client->post($url, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $data
        ]);
        $responseData = $response->getBody()->getContents();

        return intval($responseData);
    }

    public function startSolving($solverId): string
    {
        $client = new Client();

        $solveUrl = sprintf('%s%s/solve/%s', $this->url, self::TIMETABLE_URI, $solverId);
        $response = $client->post($solveUrl,
            [
                'headers' => ['Content-Type' => 'application/json'],
            ]
        );

        return $response->getBody()->getContents();
    }

    public function stopSolving($solverId): string
    {
        $client = new Client();

        $solveUrl = sprintf('%s%s/stop-solving/%s', $this->url, self::TIMETABLE_URI, $solverId);
        $response = $client->post($solveUrl,
            [
                'headers' => ['Content-Type' => 'application/json'],
            ]
        );

        return $response->getBody()->getContents();
    }

    public function getResult($solverId): string
    {
        $client = new Client();
        $getUrl = sprintf('%s%s/%s', $this->url, self::TIMETABLE_URI, $solverId);
        $response = $client->get($getUrl,
            ['headers' => ['Content-Type' => 'application/json']]
        );

        return $response->getBody()->getContents();
    }

    public function getType(): string
    {
        return $this->type;
    }
}