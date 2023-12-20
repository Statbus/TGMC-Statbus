<?php

namespace App\Service;

use Symfony\Component\Yaml\Yaml;

class AdminRankService
{
    public array $ranks;

    private const PLAYER_RANK = 'Player';

    public function __construct()
    {
        $this->ranks = self::getRankData();
    }

    public static function getRankData(): array
    {
        return Yaml::parseFile(__DIR__.'/../../assets/ranks.json');
    }

    public static function getRank(?string $rank, array $data = []): array
    {
        if(!$rank) {
            $rank = self::PLAYER_RANK;
        }
        if(!$data) {
            $data = self::getRankData();
        }
        if(empty($data[$rank])) {
            $rankData = $data[self::PLAYER_RANK];
        } else {
            $rankData = $data[$rank];
        }
        $rankData['title'] = $rank;
        return $rankData;
    }

}
