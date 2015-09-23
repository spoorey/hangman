<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 09.09.2015
 * Time: 10:34
 */
return [
    'navigation'      => [
        'default' => [
            [
                'label' => 'Home',
                'route' => 'home',
            ],
            [
                'label' => 'Wörterliste',
                'route' => 'application/words',
            ],
            [
                'label'      => 'Spiel',
                'route'      => 'application/game',
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ],
    ],
];