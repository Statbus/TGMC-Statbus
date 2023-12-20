<?php

namespace App\Action\Me\Bans;

use App\Action\Action;
use App\Domain\Ban\Repository\BanRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class ViewBansAction extends Action
{
    #[Inject]
    private BanRepository $banRepository;

    public function action(): Response
    {
        $page = (int) $this->getArg('page') ?: 1;
        $bans = $this->banRepository->getBansByCkey(
            $this->getUser()->getCkey(),
            $page
        );
        return $this->render('me/bans/index.html.twig', [
            'bans' => $bans,
            'pagination' => [
                'pages' => $this->banRepository->getPages('b.id'),
                'currentPage' => $page,
                'url' => $this->getUriForRoute($this->getRoute()->getRoute()->getName())
            ],
        ]);
    }

}
