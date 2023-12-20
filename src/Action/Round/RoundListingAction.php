<?php

namespace App\Action\Round;

use App\Action\Action;
use App\Domain\Round\Repository\RoundRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

final class RoundListingAction extends Action
{

    #[Inject]
    private RoundRepository $roundRepository;

    public function action(): Response
    {

        $page = ($this->getArg('page')) ?: 1;
        $rounds = $this->roundRepository->getRounds($page);

        return $this->render('rounds/index.html.twig', [
            'rounds' => $rounds,
            'pagination' => [
                'pages' => $this->roundRepository->getPages('b.id'),
                'currentPage' => $page,
                'url' => $this->getUriForRoute($this->getRoute()->getRoute()->getName())
            ],
        ]);
    }
}
