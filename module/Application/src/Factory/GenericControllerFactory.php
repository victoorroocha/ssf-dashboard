<?php
namespace Application\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\Adapter;
use Application\Service\OracleService;

use ReflectionClass;
use ReflectionParameter;

class GenericControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Obtém a configuração dos bancos de dados
        $config = $container->get('config')['db'];

        // Instancia o adaptador do PostgreSQL
        $pgAdapter = new Adapter($config);  // Conexão com PostgreSQL

        // Instancia o serviço Oracle, se necessário
        $oracleService = null;
        if (isset($container->get('config')['oracle'])) {
            $oracleService = $container->get('Application\Service\OracleService');
        }

        // ACL
        $acl = $container->get('Application\Acl\AccessControl')->getAcl();

        // Prepara os argumentos básicos
        $args = [
            'pgAdapter' => $pgAdapter,
            'oracleService' => $oracleService,
            'acl' => $acl
        ];

        // Analisa o construtor da controller
        $reflection = new ReflectionClass($requestedName);
        $constructor = $reflection->getConstructor();
        if ($constructor) {
            foreach ($constructor->getParameters() as $param) {
                $paramName = $param->getName();
                
                // Se já temos esse argumento preparado, pulamos
                if (array_key_exists($paramName, $args)) {
                    continue;
                }
                
                // Para parâmetros type-hinted com classes
                if ($param->hasType() && !$param->getType()->isBuiltin()) {
                    $type = $param->getType()->getName();
                    
                    // Verifica se é um repositório
                    if (strpos($type, 'Repository') !== false && $container->has($type)) {
                        $args[$paramName] = $container->get($type);
                    }
                    // Ou outro serviço conhecido
                    elseif ($container->has($type)) {
                        $args[$paramName] = $container->get($type);
                    }
                }
            }
        }


        // Ordena os argumentos conforme a assinatura do construtor
        $orderedArgs = [];
        if ($constructor) {
            foreach ($constructor->getParameters() as $param) {
                $paramName = $param->getName();
                $orderedArgs[] = $args[$paramName] ?? null;
            }
        }

        return $reflection->newInstanceArgs($orderedArgs);
    }

    // Método para determinar qual repositório injetar com base na controller
    private function getRepositoryForController($controllerName)
    {
        // Deriva o nome do repositório automaticamente, com base no nome da controller
        // Exemplo: CreditoECobrancaController -> CreditoECobrancaRepository
        $repositoryClass = str_replace('Controller', 'Repository', $controllerName);
        
        // Verifica se a classe do repositório existe
        return class_exists($repositoryClass) ? $repositoryClass : null;
    }
}