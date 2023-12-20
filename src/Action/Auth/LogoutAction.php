<?php

namespace App\Action\Auth;

use App\Action\Action;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class LogoutAction extends Action
{
    public function action(): ResponseInterface
    {
        $session = $this->container->get(Session::class);
        $session->invalidate();
        $this->addSuccessMessage("You have been logged out");
        return $this->redirect->redirectFor($this->getResponse(), 'home');
    }
}
