<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application;

use Doctrine\DBAL\Event\Listeners\MysqlSessionInit;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Zend\Mvc\Controller\ControllerManager;

return [
    'router'          => [
        'routes' => [
            'home'  => [
                'type'    => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => [
                'type'          => 'Segment',
                'options'       => [
                    'route'    => '/',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'       => '[:controller[/:action]][/:id]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'         => '[a-zA-Z0-9_-]*',
                            ],
                            'defaults'    => [
                            ],
                        ],
                    ],
                    'words' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'       => 'words[/:id[/:action]]'  ,
                            'constraints' => [
                                'id' => '[0-9]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults'    => [
                                'controller' => 'Application\Controller\Word',
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'user' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'       => 'u[/:action]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults'    => [
                                'controller' => 'Application\Controller\User',
                                'action'     => 'login',
                            ],
                        ],
                    ],
                    'game' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'       => 'game[/:action][/:id]',
                            'constraints' => [
                                'username' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults'    => [
                                'controller' => 'Application\Controller\Game',
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'word' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'       => 'word[/:action][/:id]',
                            'constraints' => [
                                'username' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults'    => [
                                'controller' => 'Application\Controller\Word',
                                'action'     => 'index',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'factories'          => [
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            'auth' 	     => 'Application\Service\Factory\AuthenticationFactory',
        ],
        'invokables' => [
            'auth-adapter' 	=> 'Application\Authentication\Adapter',
        ]
    ],
    'controllers'     => [
        'invokables'   => [
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\User'  => 'Application\Controller\UserController',
            'Application\Controller\Word'  => 'Application\Controller\WordController',
            'Application\Controller\Game'  => 'Application\Controller\GameController',
        ],
        'initializers' => [
            'ObjectManagerInitializer' => function ($controller, $controllerManager) {
                /** @var ControllerManager $controllerManager */
                // look if the controller implements the ObjectManagerAwareInterface
                if ($controller instanceof ObjectManagerAwareInterface) {
                    // locate the EntityManager using the serviceLocator

                    $services = $controllerManager->getServiceLocator();
                    $entityManager = $services->get(EntityManager::class);
                    $entityManager->getEventManager()->addEventSubscriber(
                        new MysqlSessionInit('utf8', 'utf8_unicode_ci')
                    );
                    // set the forms EntityManager or Objectmanager, 2 names for the same thing
                    $controller->setObjectManager($entityManager);
                }
            }
        ],
    ],
    'view_manager'    => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map'             => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack'      => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],

    ],
    // Placeholder for console routes
    'console'         => [
        'router' => [
            'routes' => [
            ],
        ],
    ], 'doctrine'     => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity']
            ],
            'orm_default'             => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ]
];
