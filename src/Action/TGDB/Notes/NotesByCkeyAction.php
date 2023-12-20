<?php

namespace App\Action\TGDB\Notes;

use App\Action\Action;
use App\Domain\Note\Repository\NoteRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class NotesByCkeyAction extends Action
{
    #[Inject]
    private NoteRepository $noteRepository;

    public function action(): Response
    {
        $page = ($this->getArg('page')) ?: 1;
        $ckey = $this->getArg('ckey');
        return $this->render('tgdb/notes/byCkey.html.twig', [
            'notes' => $this->noteRepository->getNotesForCkey($ckey, true, $page),
            'pagination' => [
                'pages' => $this->noteRepository->getPages(),
                'currentPage' => $page,
                'url' => $this->getUriForRoute($this->getRoute()->getRoute()->getName(), ['ckey' => $ckey])
            ],
        ]);
    }

}
