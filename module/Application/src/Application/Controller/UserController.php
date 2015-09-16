<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 16.06.2015
 * Time: 13:32
 */

namespace Application\Controller;


use Application\Entity\Word;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController implements ObjectManagerAwareInterface {

    use ProvidesObjectManager;

    public function indexAction(){

        die('use this');
    }

    public function detailsAction(){
        $userName = $this->params()->fromRoute('username');
        $userRepo = $this->getObjectManager()->getRepository(Word::class);
        $user = $userRepo->findOneBy(['userName' => $userName]);
        if (!$user instanceof Word) {
            $user = new Word();
            $user->setWord($userName);
            $this->getObjectManager()->persist($user);
            $this->getObjectManager()->flush();
            $viewModel = new ViewModel();
            $viewModel->setTemplate('error/404');
            $viewModel->setVariable('reason', 'Es wurde kein User mit dem Namen "' . $userName . '" gefunden.');
            return $viewModel;
        }

        echo 'spying on ' . $this->params()->fromRoute('username');
        die('Spymaster?');
    }
} 