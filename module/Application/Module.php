<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Controller\UserController;
use Application\Entity\User;
use Application\View\Helper\BootstrapFormRowHelper;
use Application\View\Helper\IdentityHelper;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Sql\Ddl\Column\Time;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Application\View\Helper\TimeAgoHelper;
use Zend\Mvc\I18n\Translator;
use Zend\I18n\Translator\Translator as I18nTranslator;
use Zend\Mvc\Router\RouteMatch;
use Zend\Validator\AbstractValidator;
use Zend\View\HelperPluginManager;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ViewHelperProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(MvcEvent::EVENT_ROUTE, [$this, 'protectPage'], -100);

        $t = new I18nTranslator();
        $t->setLocale('de_DE');
        $translator = new Translator($t);
        $translator->addTranslationFile(
            'phpArray',
            'vendor/zendframework/zend-i18n-resources/languages/de/Zend_Validate.php',
            'default',
            'de_DE'
        );


        AbstractValidator::setDefaultTranslator($translator);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }


    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                'timeAgo' => function() {
                    $helper = new TimeAgoHelper();
                    return $helper;
                },
                'btFormRow' => function() {
                    $helper = new BootstrapFormRowHelper();
                    return $helper;
                },
                'identity' => function($pluginManager) {
                    /** @var HelperPluginManager $pluginManager*/
                    $auth = $pluginManager->getServiceLocator()->get('auth');
                    return new IdentityHelper($auth);
                },
            ]
        ];
    }


    public function protectPage(MvcEvent $event)
    {
        $match = $event->getRouteMatch();

        $response = $event->getResponse();
        if(!$match instanceof RouteMatch || $response instanceof \Zend\Console\Response) {

            return;
        }
        $controller = $match->getParam('controller');
        $action     = $match->getParam('action');

        // Do not protect index, word list and login
        if (!(
                $controller == 'Application\Controller\Index'
                || ($controller == 'Application\Controller\Word' && $action == 'index')
                || ($controller == 'Application\Controller\User' && $action == 'login')
                || ($controller == 'Application\Controller\User' && $action == 'register')
        )) {
            $serviceManager = $event->getApplication()->getServiceManager();
            /** @var AuthenticationService $auth */
            $auth = $serviceManager->get('auth');
            $user = $auth->getIdentity();

            // check if the user is logged in. For anything to do with words (except looking at them), the user must be an admin
            if (!$user instanceof User || ($user->getRole() != 'admin' && $controller == 'Application\Controller\Word' && $action != 'index')) {
                $response->setStatusCode(401);
                $match->setParam('controller', 'Application\Controller\User');
                $match->setParam('action', 'login');

            }
        }
    }
}