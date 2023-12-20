<?php

namespace App\Action\Me\Notes;

use App\Action\Action;
use App\Domain\Note\Repository\NoteRepository;
use App\Exception\StatbusUnauthorizedException;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class ViewNoteAction extends Action
{
    #[Inject]
    private NoteRepository $noteRepository;

    public function action(): Response
    {
        $id = $this->getArg('id');
        $note = $this->noteRepository->getNoteById($id);
        if($note->getTarget() !== $this->getUser()->getCkey()) {
            throw new StatbusUnauthorizedException("This note does not belong to you");
        }
        return $this->render('me/notes/single.html.twig', [
            'note' => $note,
        ]);
    }

}
