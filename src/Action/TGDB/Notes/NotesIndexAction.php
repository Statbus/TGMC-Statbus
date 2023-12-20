<?php

namespace App\Action\TGDB\Notes;

use App\Action\Action;
use App\Domain\Note\Repository\NoteRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class NotesIndexAction extends Action
{
    #[Inject]
    private NoteRepository $noteRepository;

    public function action(): Response
    {
        $page = ($this->getArg('page')) ?: 1;
        return $this->render('tgdb/notes/index.html.twig', [
            'notes' => $this->noteRepository->getNotes($page),
            'pagination' => [
                'pages' => $this->noteRepository->getPages('n.id'),
                'currentPage' => $page,
                'url' => $this->getUriForRoute($this->getRoute()->getRoute()->getName())
            ],
        ]);
    }

}
