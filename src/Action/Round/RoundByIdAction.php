<?php

namespace App\Action\Round;

use App\Action\Action;
use App\Domain\Round\Repository\RoundRepository;
use App\Domain\Stat\Repository\StatRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

final class RoundByIdAction extends Action
{
    #[Inject]
    private RoundRepository $roundRepository;

    #[Inject]
    private StatRepository $statRepository;

    public function action(): Response
    {

        $id = (int) $this->getArg('id');
        $round = $this->roundRepository->getRound($id);
        $stats = $this->statRepository->getStatsForRound($round->getId());
        return $this->render('rounds/single.html.twig', [
            'round' => $round,
            'stats' => $stats
        ]);
    }
}
