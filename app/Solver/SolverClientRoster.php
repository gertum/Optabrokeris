<?php

namespace App\Solver;

use GuzzleHttp\Client;

class SolverClientRoster implements SolverClient
{
    private const URI_SCHEDULE = "/schedule";
    private const URI_SCHEDULE_ID = "/schedule/%s";
    private const URI_SOLVE = "/schedule/%s/solve";
    private const URI_STOP_SOLVING = "/schedule/%s/stop-solving";

    private string $type;
    private string $url;


    public function __construct(string $type, string $url)
    {
        $this->type = $type;
        $this->url = $url;
    }

    public function getCount(): int
    {
        $client = new Client();

        $url = $this->url . self::URI_SCHEDULE;
        $response = $client->get($url, [
            'headers' => ['Content-Type' => 'application/json'],
        ]);

        $responseData = $response->getBody()->getContents();

        return intval($responseData);
    }

    public function getResult($id): string
    {
        $client = new Client();

        $url = $this->url . sprintf(self::URI_SCHEDULE_ID, $id);
        $response = $client->get($url, [
            'headers' => ['Content-Type' => 'application/json'],
        ]);

        return $response->getBody()->getContents();
    }

    public function registerData($data): int
    {
        $client = new Client();

        $url = $this->url . self::URI_SCHEDULE;
        $response = $client->post($url, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $data,
        ]);

        $responseData = $response->getBody()->getContents();

        return intval($responseData);
    }


    public function startSolving($id): string
    {
        $client = new Client();

        $url = $this->url . sprintf(self::URI_SOLVE, $id);
        $response = $client->post($url, [
            'headers' => ['Content-Type' => 'application/json'],
        ]);

        return $response->getBody()->getContents();
    }

    public function stopSolving(int $id): array
    {
        $client = new Client();

        $url = $this->url . sprintf(self::URI_STOP_SOLVING, $id);
        $response = $client->post($url, [
            'headers' => ['Content-Type' => 'application/json'],
        ]);

        $responseData = $response->getBody()->getContents();

        return json_decode($responseData);
    }

    public function getType(): string
    {
        return $this->type;
    }
}