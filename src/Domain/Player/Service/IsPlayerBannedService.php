<?php

namespace App\Domain\Player\Service;

use App\Domain\Player\Data\Standing;
use App\Domain\Ban\Repository\BanRepository;
use DI\Attribute\Inject;

class IsPlayerBannedService
{
    #[Inject]
    private BanRepository $banRepository;

    public function isPlayerBanned($ckey): array
    {
        $standing = [];
        $standing['bans'] = (array) $this->banRepository->getPlayerStanding($ckey);
        if (!$standing['bans']) {
            $standing['status'] = Standing::NOT_BANNED;
            return $standing;
        }

        foreach ($standing['bans'] as $b) {
            $b = (array) $b;
            $b['perm'] = (isset($b['expiration_time'])) ? false : true;
        }
        if ($b['perm'] && 'Server' === $b['role']) {
            $standing['status'] = Standing::PERMABANNED;
            return $standing;
        } else {
            $standing['status'] = Standing::ACTIVE_BANS;
        }
        return $standing;
    }

}
