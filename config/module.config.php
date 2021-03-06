<?php
namespace Boxspaced\CmsAccountModule;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Zend\Router\Http\Segment;
use Zend\Authentication\AuthenticationService;
use Boxspaced\CmsCoreModule\Model\RepositoryFactory;

return [
    'account' => [
        'password_hashing_algorithm' => '',
    ],
    'router' => [
        'routes' => [
            // LIFO
            'account' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/account[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AccountController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            // LIFO
        ],
    ],
    'service_manager' => [
        'factories' => [
            AuthenticationService::class => Auth\AuthenticationServiceFactory::class,
            Auth\Acl::class => Auth\AclFactory::class,
            Service\AccountService::class => Service\AccountServiceFactory::class,
            Model\UserRepository::class => RepositoryFactory::class,
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\AccountController::class => Controller\AccountControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'entity_manager' => [
        'types' => [
            Model\User::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'user',
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'type' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'username' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'email' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'password' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'last_login' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'this_login' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'activated' => [
                            'type' => AbstractEntity::TYPE_BOOL,
                        ],
                        'ever_been_activated' => [
                            'type' => AbstractEntity::TYPE_BOOL,
                        ],
                        'registered_time' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
