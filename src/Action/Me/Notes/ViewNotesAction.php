<?php

namespace App\Action\Me\Notes;

use App\Action\Action;
use App\Domain\Note\Repository\NoteRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class ViewNotesAction extends Action
{
    #[Inject]
    private NoteRepository $noteRepository;

    public function action(): Response
    {
        $page = $this->getArg('page') ?: 1;
        $notes = $this->noteRepository->getNotesForCkey($this->getUser()->getCkey(), false, $page);
        return $this->render('me/notes/index.html.twig', [
            'notes' => $notes,
            'pagination' => [
                'pages' => $this->noteRepository->getPages('b.id'),
                'currentPage' => $page,
                'url' => $this->getUriForRoute($this->getRoute()->getRoute()->getName())
            ],
        ]);
    }

}
