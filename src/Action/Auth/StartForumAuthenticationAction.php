<?php

namespace App\Action\Auth;

use App\Action\Action;
use App\Provider\TGForumOAuthProvider;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class StartForumAuthenticationAction extends Action
{
    public function action(): ResponseInterface
    {
        $session = $this->container->get(Session::class);
        $settings = $this->container->get('settings')['auth']['forum'];
        $settings['redirectUri'] = $this->getUriForRoute('auth.forum.check');
        $provider = new TGForumOAuthProvider($settings);
        $authUrl = $provider->getAuthorizationUrl();
        $session->set('oauth2state', $provider->getState());
        return $this->redirect->redirect($this->getResponse(), $authUrl);
    }

}
