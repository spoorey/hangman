<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Entity\Word;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    public function indexAction()
    {
        $viewModel = new ViewModel();
        $auth = $this->getServiceLocator()->get('auth');
        if ($auth->hasIdentity()) {
            $viewModel->setTemplate('application/index/index');
        } else {
            $viewModel->setTemplate('application/index/index-notloggedin');
        }
        return $viewModel;
    }
}
