<?php
namespace Application;

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
$translator = new \Zend\I18n\Translator\Translator;
return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'zfcuser' => [
                'child_routes' => [
                    'register' => [
                        'options' => [
                            'defaults' => [
                                'controller' => null
                            ]
                        ]
                    ]
                ]
            ],
            'unauthorized' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/unauthorized',
                    'defaults' => [
                        'controller' => 'Application\Controller\Error',
                        'action'     => 'unauthorized',
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
        'aliases' => [
            'translator' => 'MvcTranslator',
            'Zend\Authentication\AuthenticationService' => 'zfcuser_auth_service'
        ],
        'factories' => [
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'ChangeLanguageDetector.listener' => 'Application\Listener\ChangeLanguageDetectorFactory',
        ]
    ],
    'controllers' => [
        'invokables' => [
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Error' => 'Application\Controller\ErrorController',
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            'TranslatorPlugin' => 'Application\Controller\Plugin\TranslatorPluginFactory'
        ]
    ],
    'translator' => [
        'locale' => 'it_IT',
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo'
            ],
            [
                'type' => 'phparray',
                'base_dir' => __DIR__. '/../language/validator',
                'pattern' => '%s.php'
            ]
        ]
    ],
    'translation_config' => [
        'languages' => [
            'it' => [
                "locale" => "it_IT",
                "lang" => "it",
                "lang_3chars" => "ita",
                "label" => "Italiano"
            ],
        ],
        "language_folder" => __DIR__ . "/../language"
    ],
    'language_detector_listeners' => [
        'factories' => [
            'LanguageFromSessionDetectorListener' => 'Application\Listener\LanguageFromSessionDetectorListenerFactory'
        ]
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'error/unauthorized'      => __DIR__ . '/../view/error/unauthorized.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],

    // Placeholder for console routes
    'console' => [
        'router' => [
            'routes' => [
                'register' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'register [<email>]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ConsoleUser',
                            'action' => 'register'
                        ]
                    ]
                ]
            ],
        ],
    ],
    
    'doctrine'        => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity']
            ],
            'orm_default'             => [
                'class'   => 'Doctrine\ORM\Mapping\Driver\DriverChain',
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ],
        ],
    ],

    // ACL
    'bjyauthorize' => [
        'guards' => [
            'BjyAuthorize\Guard\Controller' => [
                // Enable access to ZFC User pages
                ['controller' => 'zfcuser', 'roles' => []],

                ['controller' => 'Application\Controller\Error', 'roles' => []],
                ['controller' => 'Application\Controller\Index', 'roles' => ['superadmin']],
            ],
        ],
    ],
    
    // assets
    'asset_manager' => [
        'resolver_configs' => [
            'paths' => [
                __DIR__ . '/../public',
            ],
        ],
    ],

    // navigation
    'navigation' => [
        'default' => [
            [
                'label'     => $translator->translate('Example'),
                'route'     => 'home',
                'icon'      => 'fa fa-users',
                'isRouteJs' => true,
                'pages'     => [
                    [
                        'label' => $translator->translate('Link'),
                        'route' => 'home',
                        'isVisible' => true
                    ]
                ],
            ],
        ]
    ]
    
];
