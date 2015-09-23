<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 20.09.2015
 * Time: 18:01
 */

namespace Application\Service\Factory;

use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthenticationFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $adapter = $serviceLocator->get('auth-adapter');

        $auth = new AuthenticationService();
        $auth->setAdapter($adapter);

        return $auth;
    }

}
