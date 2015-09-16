<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\View\Helper\BootstrapFormRowHelper;
use Zend\Db\Sql\Ddl\Column\Time;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Application\View\Helper\TimeAgoHelper;
use Zend\Mvc\I18n\Translator;
use Zend\I18n\Translator\Translator as I18nTranslator;
use Zend\Validator\AbstractValidator;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ViewHelperProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);


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
                }
            ]
        ];
    }
}