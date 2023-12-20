<?php

namespace App\Action\Me\Bans;

use App\Action\Action;
use App\Domain\Ban\Repository\BanRepository;
use App\Exception\StatbusUnauthorizedException;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class ViewBanAction extends Action
{
    #[Inject]
    private BanRepository $banRepository;

    public function action(): Response
    {

        $ban = $this->banRepository->getBan((int) $this->getArg('id'));
        if($ban->getCkey() !== $this->getUser()->getCkey()) {
            throw new StatbusUnauthorizedException("This ban does not belong to you");
        }
        return $this->render('me/bans/single.html.twig', [
            'ban' => $ban,
        ]);
    }

}
