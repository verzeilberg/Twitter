<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Twitter;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'twitter' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/twitter[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Factory\IndexControllerFactory::class
        ],
    ],
    'service_manager' => [
        'invokables' => [
            Service\twitterServiceInterface::class => Service\twitterService::class,
            Service\twitterOathServiceInterface::class => Service\twitterOathService::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'twitter' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'twitter_credentials' => [
        'oauth_access_token' => '',
        'oauth_access_token_secret' => '',
        'consumer_key' => 'YguWLyI3fltBiq1VSqfQQ',
        'consumer_secret' => 'CjpZhinCu6jRxc0wTGwIJyGko3NxO1QDtHZEZFl6EE',
        'userId' => '320650214'
    ]
];
