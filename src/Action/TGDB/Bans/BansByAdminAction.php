<?php

namespace App\Action\TGDB\Bans;

use App\Action\Action;
use App\Domain\Ban\Repository\BanRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class BansByAdminAction extends Action
{
    #[Inject]
    private BanRepository $banRepository;

    public function action(): Response
    {
        $ckey = $this->getArg('ckey');
        $page = ($this->getArg('page')) ?: 1;
        return $this->render('tgdb/bans/bansByAdmin.html.twig', [
            'bans' => $this->banRepository->getBansByAdmin($ckey, $page),
            'ckey' => $ckey
        ]);
    }

}
