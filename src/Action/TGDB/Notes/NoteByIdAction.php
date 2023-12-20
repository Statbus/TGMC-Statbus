<?php

namespace App\Action\TGDB\Notes;

use App\Action\Action;
use App\Domain\Note\Repository\NoteRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class NoteByIdAction extends Action
{
    #[Inject]
    private NoteRepository $noteRepository;

    public function action(): Response
    {
        $id = $this->getArg('id');
        $note = $this->noteRepository->getNoteById($id);
        return $this->render('tgdb/notes/single.html.twig', [
            'note' => $note,
            'hotbar' => [
                [
                    'url' => 'tgdb.notes',
                    'params' => [],
                    'icon' => 'fas fa-chevron-left',
                    'title' => 'All Notes'
                ],
                [
                    'url' => 'tgdb.player',
                    'params' => ['ckey' => $note->getTarget()],
                    'icon' => 'fas fa-user',
                    'title' => "Player Page for ".$note->getTarget()
                ],
                [
                    'url' => 'tgdb.notes.ckey',
                    'params' => ['ckey' => $note->getTarget()],
                    'icon' => 'fas fa-note-sticky',
                    'title' => "All Notes for ".$note->getTarget()
                ],
                [
                    'url' => 'tgdb.notes.admin',
                    'params' => ['ckey' => $note->getAdmin()],
                    'icon' => 'fas fa-note-sticky',
                    'title' => "All Notes by ".$note->getAdmin()
                ],
            ]
        ]);
    }

}
