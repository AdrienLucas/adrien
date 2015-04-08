<?php

namespace SensioLabs\JobBoardBundle\Security;

use Doctrine\ORM\EntityManager;
use SensioLabs\JobBoardBundle\Entity\User;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    protected $services;

    function __construct(Container $services)
    {
        $this->services = $services;
    }

    public function loadUserByUsername($uuid)
    {
        $user = $this->$services->get('doctrine')->getRepository('SensioLabsJobBoardBundle:User')->findOneByUuid($uuid);

        if (!$user) {
            $user = new User((string) $uuid);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('class %s is not supported', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUuid());
    }

    public function supportsClass($class)
    {
        return 'SensioLabs\JobBoardBundle\Entity\User' === $class;
    }
}
