<?php

namespace App\Domain\User\Service;

use App\Domain\User\Repository\UserRepository;
use App\Domain\User\Entity\User;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class AuthenticateUser
{
    private UserRepository $userRepository;

    private Session $session;

    public function __construct(
        private ContainerInterface $container,
    ) {
        $this->userRepository = new UserRepository($container);
        $this->session = $container->get(Session::class);
    }

    public function refreshUser(): ?User
    {
        $ckey = $this->session->get('ckey', false);
        if(!$ckey) {
            return null;
        }
        $user = $this->userRepository->getUserByCkey($ckey);
        $user->setSource($this->session->get('authSource', 'Manual Override'));
        return $user;
    }

    public function authenticateUserFromForum(string $ckey): User
    {
        $user = $this->userRepository->getUserByCkey($ckey);

        $user->setSource('TG Forum');

        $this->session->set('authSource', 'TG Forum');
        $this->session->set('ckey', $user->getCkey());

        return $user;
    }

}
