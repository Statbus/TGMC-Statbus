<?php

namespace App\Action\TGDB\Player;

use App\Action\Action;
use App\Domain\Ban\Repository\BanRepository;
use App\Domain\Note\Repository\NoteRepository;
use App\Domain\Player\Repository\PlayerRepository;
use App\Domain\Player\Service\IsPlayerBannedService;
use App\Service\HotbarCreationService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class GetPlayerAction extends Action
{
    #[Inject]
    private PlayerRepository $playerRepository;

    #[Inject]
    private IsPlayerBannedService $bannedService;

    #[Inject]
    private BanRepository $banRepository;

    #[Inject]
    private NoteRepository $noteRepository;

    public function action(): Response
    {
        $ckey = $this->getArg('ckey');
        $player = $this->playerRepository->getPlayer($ckey);
        $bans = $this->banRepository->getBansByCkey($player->getCkey(), 1, 5);
        $notes = $this->noteRepository->getNotesForCkey($player->getCkey(), true, 1, 5);
        $standing = $this->bannedService->isPlayerBanned($ckey);
        $hotbar = HotbarCreationService::create()
            ->addLink("Notes for ".$player->getCkey(), "fas fa-sticky-note", 'tgdb.notes.ckey', ['ckey' => $player->getCkey()])
            ->addLink("Bans for ".$player->getCkey(), "fas fa-hammer", 'tgdb.bans.ckey', ['ckey' => $player->getCkey()])
            ->addLink("Rounds for ".$player->getCkey(), "fas fa-circle", 'home', ['ckey' => $player->getCkey()]);
        return $this->render('tgdb/player/single.html.twig', [
            'player' => $player,
            'standing' => $standing,
            'bans' => $bans,
            'notes' => $notes,
            'hotbar' => $hotbar->get(),
        ]);
    }

}
