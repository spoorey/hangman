<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 20.09.2015
 * Time: 18:09
 */
namespace Application\Authentication;

use Doctrine\ORM\EntityManager;
use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Authentication\Result;
use Application\Entity\User;

class Adapter extends AbstractAdapter implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function authenticate()
    {
        $entityManager = $this->serviceLocator->get(EntityManager::class);
        $user = $entityManager->getRepository(User::class)->findOneBy(['userName' => $this->identity]);

        if ($user instanceof User && $user->verifyPassword($this->credential)) {
            // upon successful validation we can return the user entity object
            return new Result(Result::SUCCESS, $user);
        }

        return new Result(Result::FAILURE, $this->identity);
   }
}
 