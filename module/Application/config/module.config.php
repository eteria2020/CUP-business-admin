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
            'employees' => [
                'type' => 'Segment',
                'options' => [
                    'route'    => '/employees',
                    'defaults' => [
                        'controller' => 'Application\Controller\Employees',
                        'action'     => 'employees',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'employee' => [
                        'type' => 'Segment',
                        'options' => [
                            'route'    => '/:id',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'approve' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/approve',
                                    'defaults' => [
                                        'action' => 'approve-employee',
                                    ],
                                ],
                            ],
                            'remove' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/remove',
                                    'defaults' => [
                                        'action' => 'remove-employee',
                                    ],
                                ],
                            ],
                            'block' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/block-employee',
                                    'defaults' => [
                                        'action' => 'block-employee',
                                    ],
                                ],
                            ],
                            'unblock' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/unblock-employee',
                                    'defaults' => [
                                        'action' => 'unblock-employee',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ],
            'groups' => [
                'type' => 'Segment',
                'options' => [
                    'route'    => '/groups',
                    'defaults' => [
                        'controller' => 'Application\Controller\Groups',
                        'action'     => 'groups',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'add' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/add',
                            'defaults' => [
                                'action' => 'add',
                            ],
                        ],
                    ],
                    'details' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/:id',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'action' => 'details',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'add-employees' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/add-employees',
                                    'defaults' => [
                                        'action' => 'add-employees-to-group',
                                    ],
                                ],
                            ],
                            'remove-employee' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/remove/:employee',
                                    'defaults' => [
                                        'action' => 'remove-employee-from-group',
                                    ],
                                ],
                            ],
                        ]
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
            'doctrine.connection.orm_default' => 'Application\Service\OrmConnectionFactory',
        ],
        'invokables' => [
            'Application\Form\GroupForm' => 'Application\Form\GroupForm'
        ]
    ],
    'controllers' => [
        'invokables' => [
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Error' => 'Application\Controller\ErrorController',
        ],
        'factories' => [
            'Application\Controller\Employees' => 'Application\Controller\EmployeesControllerFactory',
            'Application\Controller\Groups' => 'Application\Controller\GroupsControllerFactory',
        ]
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
    // ACL
    'bjyauthorize' => [
        'guards' => [
            'BjyAuthorize\Guard\Controller' => [
                // Enable access to ZFC User pages
                ['controller' => 'zfcuser', 'roles' => []],

                ['controller' => 'Application\Controller\Error', 'roles' => []],
                ['controller' => 'Application\Controller\Index', 'roles' => ['superadmin']],
                ['controller' => 'Application\Controller\Employees', 'roles' => []],
                ['controller' => 'Application\Controller\Groups', 'roles' => []],
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
                'label'     => $translator->translate('Dipendenti'),
                'route'     => 'employees',
                'icon'      => 'fa fa-users',
                'isRouteJs' => true,
                'pages'     => [
                    [
                        'label' => $translator->translate('Lista'),
                        'route' => 'employees',
                        'isVisible' => true
                    ],
                    [
                        'label' => $translator->translate('Gestione gruppi'),
                        'route' => 'groups',
                        'isVisible' => true
                    ],
                ],
            ],
        ],
    ],
];
