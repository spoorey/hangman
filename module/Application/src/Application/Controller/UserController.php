<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 16.06.2015
 * Time: 13:32
 */

namespace Application\Controller;


use Application\Authentication\Adapter;
use Application\Entity\User;
use Application\Form\LoginForm;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController implements ObjectManagerAwareInterface
{

    use ProvidesObjectManager;

    public function detailsAction()
    {
        $userName = $this->params()->fromRoute('username');
        $userRepo = $this->getObjectManager()->getRepository(User::class);
        $user = $userRepo->findOneBy(['userName' => $userName]);
        if (!$user instanceof User) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('error/404');
            $viewModel->setVariable('reason', 'Es wurde kein User mit dem Namen "' . $userName . '" gefunden.');

            return $viewModel;
        }

        echo $user->getUserName();
    }

    public function logInAction()
    {
        $request = $this->getRequest();
        $form = new LoginForm();
        $loginFailed = false;

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();

                /** @var Adapter $auth */
                $auth = $this->serviceLocator->get('auth');

                $authAdapter = $auth->getAdapter();
                $authAdapter->setIdentity($data['username']);
                $authAdapter->setCredential($data['password']);
                $result = $auth->authenticate();
                if($result->isValid()) {
                    $user = $result->getIdentity();
                    return $this->redirect()->toRoute('application/game');
                } else {
                    $loginFailed = true;
                }
            }
        }



        return new ViewModel([
            'form'        => $form,
            'loginFailed' => $loginFailed,
        ]);
    }
} 