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
use Application\Form\UserForm;
use Application\InputFilter\UserInputFilter;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Zend\Crypt\Password\Bcrypt;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController implements ObjectManagerAwareInterface
{

    use ProvidesObjectManager;

    public function logInAction()
    {
        $request = $this->getRequest();
        $form = new LoginForm();
        $loginFailed = false;

        if ($request->isPost()) {
            // Check if the form and provided values are valid, and redirect if so
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

    public function registerAction() {
        $request = $this->getRequest();
        $form = new UserForm();
        $userNameConflict = false;

        if ($request->isPost()) {
            // check if the form is valid
            $form->setData($request->getPost());
            $form->setInputFilter(new UserInputFilter());
            if ($form->isValid()) {
                $data = $form->getData();

                $userRepo = $this->getObjectManager()->getRepository(User::class);
                $userNameConflict = ($userRepo->findOneBy(['userName' => $data['username']]) instanceof User);

                if ($userNameConflict) {
                    $form->get('username')->setValue('');
                } else {
                    // if the requested username is not taken yet, create the password and redirect the user to the login
                    $user = new User();
                    $user->setEmail($data['email']);
                    $user->setUserName($data['username']);
                    $bcrypt = new Bcrypt();
                    $password = $bcrypt->create($data['password']);
                    $user->setPassword($password);
                    $this->getObjectManager()->persist($user);
                    $this->getObjectManager()->flush();

                    return $this->redirect()->toRoute('application/user', ['action' => 'login']);
                }
            }
        }

        return new ViewModel([
            'form' => $form,
            'userNameConflict' => $userNameConflict,
        ]);
    }

    public function logoutAction() {
        $auth = $this->serviceLocator->get('auth');
        $auth->clearIdentity();

        return $this->redirect()->toRoute('home');
    }
}
