<?php

namespace App\Action\TGDB;

use App\Action\Action;
use Nyholm\Psr7\Response;

final class TGDBHomeAction extends Action
{
    public function action(): Response
    {
        return $this->render('tgdb/home.html.twig');
    }
}
