<?php

declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Application\Repository\CreditoECobrancaRepository;  // Importar repositório
use Application\Repository\ControladoriaRepository;  // Importar repositório
use Application\Repository\ContabilidadeRepository;  // Importar repositório
use Application\Repository\RecursosHumanosRepository;  // Importar repositório
use Application\Repository\ComercialRepository;  // Importar repositório
use Application\Repository\VendasRepository;  // Importar repositório
use Application\Repository\PlanejamentoControleManutencaoRepository;  // Importar repositório
use Application\Repository\PlanejamentoControleProducaoRepository;  // Importar repositório
use Application\Repository\TiInfraRepository;  // Importar repositório
use Application\Repository\DepartamentoRepository;  // Importar repositório
use Application\Repository\CompressRepository;  // Importar repositório

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/[:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index', 
                    ],
                ],
            ],
            'login' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action'     => 'logout',
                    ],
                ],
            ],
            'error' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/error[/:action]',
                    'defaults' => [
                        'controller' => Controller\ErrorController::class,
                        'action'     => 'index', 
                    ],
                ],
            ],
            // Teste Banco de Dados
            'db-test' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/db-test',
                    'defaults' => [
                        'controller' => Controller\DbController::class, 
                        'action'     => 'test',
                    ],
                ],
            ],
            'usuario' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/usuario[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',  
                        'id'     => '[0-9]+',  
                    ],
                    'defaults' => [
                        'controller' => Controller\UsuarioController::class,
                        'action'     => 'index',  
                    ],
                ],
            ],
            'menu' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/menu[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\MenuController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'compress' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/compress[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\CompressController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            // Departamento
            'departamento' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/departamento[/:action][/:id]',  
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',  
                        'id'     => '[0-9]+',  
                    ],
                    'defaults' => [
                        'controller' => Controller\DepartamentoController::class,
                        'action'     => 'index', 
                    ],
                ],
            ],
            // Credito e Cobrança
            'credito-e-cobranca' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/credito-e-cobranca[/:action][/:id]',  
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',  
                        'id'     => '[0-9]+',  
                    ],
                    'defaults' => [
                        'controller' => Controller\CreditoECobrancaController::class,
                        'action'     => 'index', 
                    ],
                ],
            ],
            // Controladoria
            'controladoria' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/controladoria[/:action][/:id]',  
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',  
                        'id'     => '[0-9]+',  
                    ],
                    'defaults' => [
                        'controller' => Controller\ControladoriaController::class,
                        'action'     => 'index', 
                    ],
                ],
            ],
            // Contabilidade
            'contabilidade' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/contabilidade[/:action][/:id]',  
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',  
                        'id'     => '[0-9]+',  
                    ],
                    'defaults' => [
                        'controller' => Controller\ContabilidadeController::class,
                        'action'     => 'index', 
                    ],
                ],
            ],
            // Recursos Humanos
            'recursos-humanos' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/recursos-humanos[/:action][/:id]',  
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',  
                        'id'     => '[0-9]+',  
                    ],
                    'defaults' => [
                        'controller' => Controller\RecursosHumanosController::class,
                        'action'     => 'index', 
                    ],
                ],
            ],
            // Comercial
            'comercial' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/comercial[/:action][/:id]',  
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',  
                        'id'     => '[0-9]+',  
                    ],
                    'defaults' => [
                        'controller' => Controller\ComercialController::class,
                        'action'     => 'index', 
                    ],
                ],
            ],
            // Vendas
            'vendas' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/vendas[/:action][/:id]',  
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',  
                        'id'     => '[0-9]+',  
                    ],
                    'defaults' => [
                        'controller' => Controller\VendasController::class,
                        'action'     => 'index', 
                    ],
                ],
            ],
            // Planejamento Controle Manutenção
            'planejamento-controle-manutencao' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/planejamento-controle-manutencao[/:action][/:id]',  
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',  
                        'id'     => '[0-9]+',  
                    ],
                    'defaults' => [
                        'controller' => Controller\PlanejamentoControleManutencaoController::class,
                        'action'     => 'index', 
                    ],
                ],
            ],
            // Planejamento Controle Produção
            'planejamento-controle-producao' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/planejamento-controle-producao[/:action][/:id]',  
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',  
                        'id'     => '[0-9]+',  
                    ],
                    'defaults' => [
                        'controller' => Controller\PlanejamentoControleProducaoController::class,
                        'action'     => 'index', 
                    ],
                ],
            ],
            // TI Infra
            'ti-infra' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/ti-infra[/:action][/:id]',  
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',  
                        'id'     => '[0-9]+',  
                    ],
                    'defaults' => [
                        'controller' => Controller\TiInfraController::class,
                        'action'     => 'index', 
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\MenuController::class => function($container) {
                return new Controller\MenuController(
                    $container->get('Laminas\Db\Adapter\Adapter'), // Adaptador do banco de dados
                    $container->get('Application\Repository\MenuRepository'), // Repositório de menus
                    $container->get('Application\Acl\AccessControl')->getAcl() // ACL
                );
            },
            Controller\CompressController::class => Factory\GenericControllerFactory::class,
            Controller\IndexController::class => Factory\GenericControllerFactory::class,
            Controller\DbController::class => Factory\GenericControllerFactory::class,  
            Controller\LoginController::class => Factory\LoginControllerFactory::class,
            Controller\ErrorController::class => Factory\GenericControllerFactory::class,
            Controller\UsuarioController::class => Factory\GenericControllerFactory::class,
            Controller\DepartamentoController::class => Factory\GenericControllerFactory::class,
            Controller\CreditoECobrancaController::class => Factory\GenericControllerFactory::class,
            Controller\ControladoriaController::class => Factory\GenericControllerFactory::class,
            Controller\ContabilidadeController::class => Factory\GenericControllerFactory::class,
            Controller\RecursosHumanosController::class => Factory\GenericControllerFactory::class,
            Controller\ComercialController::class => Factory\GenericControllerFactory::class,
            Controller\VendasController::class => Factory\GenericControllerFactory::class,
            Controller\PlanejamentoControleManutencaoController::class => Factory\GenericControllerFactory::class,
            Controller\PlanejamentoControleProducaoController::class => Factory\GenericControllerFactory::class,
            Controller\TiInfraController::class => Factory\GenericControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Laminas\Session\SessionManager::class => Application\Factory\SessionManagerFactory::class,
            'Application\Acl\AccessControl' => function($container) {
                return new \Application\Acl\AccessControl();
            },
            'Application\Service\OracleService' => function($container) {
                $config = $container->get('config')['oracle'];  // Supondo que a configuração do Oracle esteja em 'config'
                return new \Application\Service\OracleService(
                    $config['username'],
                    $config['password'],
                    $config['connection_string'],
                    'WE8MSWIN1252'
                );
            },
            'Application\Service\AuthService' => function($container) {
                $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);

                $oracleService = null;
                try {
                    $oracleService = $container->get(\Application\Service\OracleService::class);
                } catch (\Throwable $e) {
                    error_log('Erro ao iniciar OracleService: ' . $e->getMessage());
                }

                return new \Application\Service\AuthService($dbAdapter, $oracleService);
            },
            Application\Service\CompressService::class => Laminas\ServiceManager\Factory\InvokableFactory::class,
            'Application\Repository\UsuarioRepository' => function ($container) {
                $adapter = $container->get('Laminas\Db\Adapter\Adapter');
                return new \Application\Repository\UsuarioRepository($adapter);
            },
            'Application\Controller\UsuarioController' => function ($container) {
                $usuarioRepository = $container->get('Application\Repository\UsuarioRepository');
                return new \Application\Controller\UsuarioController($usuarioRepository);
            }, 
            'Application\Controller\MenuController' => function ($container) {
                $menuRepository = $container->get('Application\Repository\MenuRepository');
                return new \Application\Controller\MenuController($menuRepository);
            }, 
            'Application\Repository\MenuRepository' => function ($container) {
                $adapter = $container->get('Laminas\Db\Adapter\Adapter'); // Certifique-se de que o adapter está sendo injetado
                return new \Application\Repository\MenuRepository($adapter);
            },
            'Application\Repository\DepartamentoRepository' => function ($container) {
                $adapter = $container->get('Laminas\Db\Adapter\Adapter');
                return new \Application\Repository\DepartamentoRepository($adapter);
            },
            'Application\Repository\CompressRepository' => function ($container) {
                $adapter = $container->get('Laminas\Db\Adapter\Adapter');
                return new \Application\Repository\CompressRepository($adapter);
            },
            'Application\Repository\ControladoriaRepository' => function ($container) {
                $adapter = $container->get('Laminas\Db\Adapter\Adapter'); // Certifique-se de que o adapter está sendo injetado
                return new \Application\Repository\ControladoriaRepository($adapter);
            },
            'Application\Repository\ContabilidadeRepository' => function ($container) {
                $adapter = $container->get('Laminas\Db\Adapter\Adapter'); // Certifique-se de que o adapter está sendo injetado
                return new \Application\Repository\ContabilidadeRepository($adapter);
            },
            'Application\Repository\CreditoECobrancaRepository' => function ($container) {
                $adapter = $container->get('Laminas\Db\Adapter\Adapter');
                return new \Application\Repository\CreditoECobrancaRepository($adapter);
            },
            'Application\Repository\PlanejamentoControleManutencaoRepository' => function ($container) {
                $adapter = $container->get('Laminas\Db\Adapter\Adapter');
                return new \Application\Repository\PlanejamentoControleManutencaoRepository($adapter);
            },
            'Application\Repository\PlanejamentoControleProducaoRepository' => function ($container) {
                $adapter = $container->get('Laminas\Db\Adapter\Adapter');
                return new \Application\Repository\PlanejamentoControleProducaoRepository($adapter);
            },
            'Application\Repository\TiInfraRepository' => function ($container) {
                $adapter = $container->get('Laminas\Db\Adapter\Adapter');
                return new \Application\Repository\TiInfraRepository($adapter);
            },
            'Application\Repository\VendasRepository' => function ($container) {
                $adapter = $container->get('Laminas\Db\Adapter\Adapter');
                return new \Application\Repository\VendasRepository($adapter);
            },
            RecursosHumanosRepository::class => InvokableFactory::class, 
            ComercialRepository::class => InvokableFactory::class, 
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'application' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
        'display_exceptions' => false, // Não mostrar exceptions diretamente
        'exception_template' => 'error/error', // Template padrão para erros
        'template_map' => [
            'error/error' => __DIR__ . '/../view/application/error/error.phtml',
        ],
    ],
];
