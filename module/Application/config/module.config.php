<?php
namespace Application;

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
use Zend\I18n\Translator\Translator;

$translator = new Translator;
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
                                    'route' => '/block',
                                    'defaults' => [
                                        'action' => 'block-employee',
                                    ],
                                ],
                            ],
                            'unblock' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/unblock',
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
                                    'route' => '/remove-employee/:employee',
                                    'defaults' => [
                                        'action' => 'remove-employee-from-group',
                                    ],
                                ],
                            ],
                        ]
                    ],
                ],
            ],
            'trips' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/trips',
                    'defaults' => [
                        'controller' => 'Application\Controller\Trips',
                        'action'     => 'trips',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'datatable' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/datatable',
                            'defaults' => [
                                'action' => 'datatable',
                            ],
                        ],
                    ],
                ],
            ],
            'invoices' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/invoices',
                    'defaults' => [
                        'controller' => 'Application\Controller\Invoices',
                        'action'     => 'invoices',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'datatable' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/datatable',
                            'defaults' => [
                                'action' => 'datatable',
                            ],
                        ],
                    ],
                    'pdf' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/download/:id',
                            'defaults' => [
                                'action' => 'pdf',
                            ],
                        ],
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
            'Application\Controller\Trips' => 'Application\Controller\TripsControllerFactory',
            'Application\Controller\Invoices' => 'Application\Controller\InvoicesControllerFactory',
        ]
    ],
    'controller_plugins' => [
        'factories' => [
            'translatorPlugin' => 'Application\Controller\Plugin\TranslatorPluginFactory'
        ]
    ],
    'view_helpers'    => [
        'factories' => [
            'languageMenuHelper' => 'Application\View\Helper\LanguageMenuHelperFactory',
        ],
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
            'en' => [
                "locale" => "en_US",
                "lang" => "en",
                "lang_3chars" => "eng",
                "label" => "English"
            ],
            'fr' => [
                "locale" => "fr_FR",
                "lang" => "fr",
                "lang_3chars" => "fra",
                "label" => "Français"
            ],
            'zh' => [
                "locale" => "zh_CN",
                "lang" => "zh",
                "lang_3chars" => "zho",
                "label" => "中国"
            ],
            'de' => [
                "locale" => "de_DE",
                "lang" => "de",
                "lang_3chars" => "deu",
                "label" => "Deutsch"
            ],
            'es' => [
                "locale" => "es_ES",
                "lang" => "es",
                "lang_3chars" => "spa",
                "label" => "Español"
            ],
            'hu' => [
                "locale" => "hu_HU",
                "lang" => "hu",
                "lang_3chars" => "hun",
                "label" => "Magyar"
            ],
            'pl' => [
                "locale" => "pl_PL",
                "lang" => "pl",
                "lang_3chars" => "pol",
                "label" => "Polskie"
            ],
            'pt' => [
                "locale" => "pt_PT",
                "lang" => "pt",
                "lang_3chars" => "por",
                "label" => "Português"
            ],
            'ru' => [
                "locale" => "ru_RU",
                "lang" => "ru",
                "lang_3chars" => "rus",
                "label" => "Pусский"
            ],
            'tr' => [
                "locale" => "tr_TR",
                "lang" => "tr",
                "lang_3chars" => "tur",
                "label" => "Türk"
            ]
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
                ['controller' => 'Application\Controller\Index', 'roles' => ['superadmin', 'business']],
                ['controller' => 'Application\Controller\Employees', 'roles' => ['superadmin', 'business']],
                ['controller' => 'Application\Controller\Groups', 'roles' => ['superadmin', 'business']],
                ['controller' => 'Application\Controller\Trips', 'roles' => ['superadmin', 'business']],
                ['controller' => 'Application\Controller\Invoices', 'roles' => ['superadmin', 'business']],
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
                        'label' => $translator->translate('Elenco'),
                        'route' => 'employees',
                        'isVisible' => true
                    ],
                    [
                        'label' => $translator->translate('Gestione gruppi'),
                        'route' => 'groups',
                        'isVisible' => true,
                    ],
                    [
                        'route' => 'groups/add',
                        'isVisible' => false
                    ],
                    [
                        'route' => 'groups/details',
                        'isVisible' => false
                    ],
                    [
                        'route' => 'groups/details/add-employees',
                        'isVisible' => false
                    ],
                ],
            ],
            [
                'label'     => $translator->translate('Corse'),
                'route'     => 'trips',
                'icon'      => 'fa fa-road',
                'isRouteJs' => true,
                'pages'     => [
                    [
                        'label' => $translator->translate('Elenco'),
                        'route' => 'trips',
                        'isVisible' => true
                    ],
                ],
            ],
            [
                'label'     => $translator->translate('Fatture'),
                'route'     => 'invoices',
                'icon'      => 'fa fa-file-o',
                'isRouteJs' => true,
                'pages'     => [
                    [
                        'label' => $translator->translate('Elenco'),
                        'route' => 'invoices',
                        'isVisible' => true
                    ],
                ],
            ],
        ]
    ]
];
