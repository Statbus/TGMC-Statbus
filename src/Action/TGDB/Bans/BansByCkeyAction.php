<?php

namespace App\Action\TGDB\Bans;

use App\Action\Action;
use App\Domain\Ban\Repository\BanRepository;
use App\Service\HotbarCreationService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class BansByCkeyAction extends Action
{
    #[Inject]
    private BanRepository $banRepository;

    public function action(): Response
    {
        $ckey = $this->getArg('ckey');
        $page = ($this->getArg('page')) ?: 1;
        $bans = $this->banRepository->getBansByCkey($ckey, $page);
        $hotbar = HotbarCreationService::create()
            ->addLink('All Bans', 'fas fa-chevron-left', 'tgdb.bans')
            ->get();
        return $this->render('tgdb/bans/bansByCkey.html.twig', [
            'bans' => $bans,
            'ckey' => $ckey,
            'hotbar' => $hotbar
        ]);
    }

}
