<?php

namespace App\Action\TGDB\Bans;

use App\Action\Action;
use App\Domain\Ban\Repository\BanRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class BanIndexAction extends Action
{
    #[Inject]
    private BanRepository $banRepository;

    public function action(): Response
    {
        $page = ($this->getArg('page')) ?: 1;
        $bans = $this->banRepository->getBans($page);
        return $this->render('tgdb/bans/index.html.twig', [
            'bans' => $bans,
            'pagination' => [
                'pages' => $this->banRepository->getPages('b.id'),
                'currentPage' => $page,
                'url' => $this->getUriForRoute($this->getRoute()->getRoute()->getName())
            ],
        ]);
    }

}
