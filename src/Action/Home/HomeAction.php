<?php

namespace App\Action\Home;

use App\Action\Action;
use Nyholm\Psr7\Response;

final class HomeAction extends Action
{
    public function action(): Response
    {
        return $this->render('home/index.html.twig', [
        ]);
    }
}
