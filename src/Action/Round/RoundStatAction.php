<?php

namespace App\Action\Round;

use App\Action\Action;
use App\Domain\Round\Repository\RoundRepository;
use App\Domain\Stat\Repository\StatRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

final class RoundStatAction extends Action
{
    #[Inject]
    private StatRepository $statRepository;

    public function action(): Response
    {

        $round = (int) $this->getArg('round');
        $name = (string) $this->getArg('name');
        $stat  = $this->statRepository->getStat($round, $name);
        return $this->render('rounds/stat.html.twig', [
            'stat' => $stat
        ]);
    }
}
