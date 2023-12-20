<?php

namespace App\Action\Auth;

use App\Action\Action;
use App\Domain\User\Service\AuthenticateUser;
use App\Provider\TGForumOAuthProvider;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use DI\Attribute\Inject;

class FinishForumAuthenticationAction extends Action
{
    #[Inject]
    private AuthenticateUser $auth;

    public function action(): ResponseInterface
    {
        $session = $this->container->get(Session::class);
        $settings = $this->container->get('settings')['auth']['forum'];
        $settings['redirectUri'] = $this->getUriForRoute('auth.forum.check');
        $provider = new TGForumOAuthProvider($settings);

        $session = $this->container->get(Session::class);

        if(!$this->getQueryPart('code')) {
            return $this->redirect->redirectFor($this->getResponse(), 'auth.forum');
        }

        if (!$this->getQueryPart('state') || ($this->getQueryPart('state') !== $session->get('oauth2state'))) {
            $session->set('oauth2state', false);
            exit('Invalid state');
        }

        $token = $provider->getAccessToken('authorization_code', [
            'code' => $this->getQueryPart('code')
        ]);
        $forumUser = $provider->getResourceOwner($token);
        if($this->auth->authenticateUserFromForum($forumUser->getId())) {
            $this->addSuccessMessage("You have successfully authenticated via the forums!");
            if($redirect = $session->get('authRedirect')) {
                $session->set('authRedirect', false);
                return $this->redirect->redirect($this->getResponse(), $redirect);
            } else {
                return $this->redirect->redirectFor($this->getResponse(), 'home');
            }
        } else {
            die("Authentication failed");
        }
    }
}
