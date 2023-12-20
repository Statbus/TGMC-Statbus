<?php

namespace App\Service;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Symfony\Component\Yaml\Yaml;

class ServerInformationService
{
    public static function getServerInfo(string $endpoint): ?array
    {
        $stack = HandlerStack::create();
        $stack->push(new CacheMiddleware(), 'cache');
        $client = new Client([
            'timeout'  => 1.0,
            'handler' => $stack
        ]);
        try {
            $response = $client->get($endpoint);
            $data = json_decode($response->getBody(), true);
            if(!$data) {
                $data = Yaml::parseFile(__DIR__.'/../../assets/servers.json');
            }
        } catch (Exception $e) {
            $data = Yaml::parseFile(__DIR__.'/../../assets/servers.json');
        }
        if(!$data) {
            $data = Yaml::parseFile(__DIR__.'/../../assets/servers.json');
        }
        return $data;
    }

    public static function getServerFromPort(int $port, array $servers = []): array
    {
        foreach($servers as $s) {
            if(!empty($s['serverdata']) && $port === $s['serverdata']['port']) {
                return $s;
            }
        }
    }

    public static function getCurrentRounds(string $game, ?array $servers = []): array
    {
        $rounds = [];
        if($servers) {
            foreach($servers as $s) {
                if(!empty($s['version']) && $game === $s['version']) {
                    $rounds[] = (int) $s['round_id'];
                }
            }
        }
        return $rounds;
    }

}
