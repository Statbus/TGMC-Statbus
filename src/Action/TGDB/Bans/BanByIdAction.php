<?php

namespace App\Action\TGDB\Bans;

use App\Action\Action;
use App\Domain\Ban\Repository\BanRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class BanByIdAction extends Action
{
    #[Inject]
    private BanRepository $banRepository;

    public function action(): Response
    {
        $id = $this->getArg('id');
        $ban = $this->banRepository->getBan($id);
        return $this->render('tgdb/bans/single.html.twig', [
            'ban' => $ban,
            'hotbar' => [
                [
                    'url' => 'tgdb.bans',
                    'params' => [],
                    'icon' => 'fas fa-chevron-left',
                    'title' => 'All Bans'
                ],
                [
                    'url' => 'tgdb.player',
                    'params' => ['ckey' => $ban->getCkey()],
                    'icon' => 'fas fa-user',
                    'title' => "Player Page for ".$ban->getCkey()
                ],
                [
                    'url' => 'tgdb.bans.ckey',
                    'params' => ['ckey' => $ban->getCkey()],
                    'icon' => 'fas fa-hammer',
                    'title' => "All Bans for ".$ban->getCkey()
                ],
                [
                    'url' => 'tgdb.bans.admin',
                    'params' => ['ckey' => $ban->getAdmin()],
                    'icon' => 'fas fa-note-sticky',
                    'title' => "All Bans by ".$ban->getAdmin()
                ],
            ]
        ]);
    }

}
