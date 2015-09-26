<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 23.09.2015
 * Time: 15:38
 */

namespace Application\View\Helper;


use Application\Entity\User;
use Zend\Authentication\AuthenticationService;
use Zend\View\Helper\AbstractHelper;

class IdentityHelper extends AbstractHelper
{
    /**
     * @var AuthenticationService
     */
    protected $authService;

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Returns the currently logged in user
     *
     * @return User|null
     */
    public function __invoke(){
        return $this->authService->getIdentity();
    }

}
 